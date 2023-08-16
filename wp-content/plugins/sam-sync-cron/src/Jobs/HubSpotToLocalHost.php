<?php

/**
 * @package hubspot-to-localhost
 */
namespace Snapshot\SamSync\Jobs;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Snapshot\SamSync\Core\Logging\LoggerFactory;
use Snapshot\SamSync\Core\Integrations\SalesLink\SalesLinkApiClient;
use Snapshot\SamSync\Core\Integrations\SalesLink\Utils as SalesLinkApiUtils;
use Snapshot\SamSync\Core\Integrations\HubSpot\HubSpotApiClient;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;
use PDO;

class HubSpotToLocalHost
{
    protected const CRON_HOOK = 'HS2_local_host';
    protected const SCHEDULE_NAME = 'HS2_local_host_sch';
    

    /**
     * Registers a WordPress Cron Task
     *
     * @return void
     */
    public static function init()
    {
      
        $logger = LoggerFactory::make('HubSpotToLocalHost');
        $instance = new HubSpotToLocalHost($logger);

        add_filter('cron_schedules', function ($schedules) {
            $schedules[static::SCHEDULE_NAME] = [
                'interval' => 3600,
                'display' => __('HS2local: contact sync'),
            ];

            return $schedules;
        });

        add_action(static::CRON_HOOK, function () use ($instance) {
            $instance->execute();
        });

        if (! wp_next_scheduled(static::CRON_HOOK)) {
            $logger->info('HubSpotToLocalHost: Scheduled job!');
            wp_schedule_event(time(), static::SCHEDULE_NAME, static::CRON_HOOK);
        }
    }

    /**
     * Unregisters the WordPress Cron Task
     *
     * @return void
     */
    public static function destroy()
    {
        $logger = LoggerFactory::make('HubSpotToLocalHost');
        $timestamp = wp_next_scheduled(static::CRON_HOOK);
        wp_unschedule_event($timestamp, static::CRON_HOOK);

        $logger->info('HubSpotToLocalHost: Unregistered job!');
    }

    protected Logger $logger;
    protected SalesLinkApiClient $salesLinkClient;
    protected HubSpotApiClient $hubSpotClient;
    protected $servername;
    protected $username;
    protected $password;
    protected $dbname;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->salesLinkClient = SalesLinkApiClient::make($logger);
        $this->hubSpotClient = HubSpotApiClient::make($logger);
        $this->servername = DB_HOST;
        $this->username = DB_USER;
        $this->password = DB_PASSWORD;
        $this->dbname = DB_NAME;
        $this->execute();
    }

    /**
     * Job executor
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->logRun();  

            // 1. Insert Hubspot contacts in local
            $updatedContacts = $this->insertHubSpotContactsInLocal();

            $this->logger->info('HubSpotToLocalHost: updated ' . $updatedContacts . ' contacts in HubSpot', [
                'opps' => $updatedContacts,
            ]);

            if ($updatedContacts === 0) {
                return false;
            }

            $this->logger->info('HubSpotToLocalHost: Job complete!', [
                'success' => 'Inserted hubspot contacts in local'
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('HubSpotToLocalHost: Could not complete execution:');
            $this->logger->error($e);

            return false;
        }
    }

    /**
     * Resets the unique request ID and then logs invocation
     *
     * @return void
     */
    protected function logRun(): void
    {
        foreach ($this->logger->getProcessors() as $processor) {
            if ($processor instanceof UidProcessor) {
                $processor->reset();
            }
        }

        $this->logger->info('HubSpotToLocalHost: Executing...');
    }

    /**
     * Loads opportunities from SalesLink
     *
     * @return void
     */
    protected function insertHubSpotContactsInLocal()
    {
        $builder = new MySqlBuilder(); 
        $pdo = new PDO('mysql:host=' . $this->servername . ';dbname='.$this->dbname, $this->username, $this->password);
        try {
            $isLast = false;
            $next = 'false';
            $i = 0;
            $inserted = 0;
            do {
                $i = $i++;
                $response = $this->hubSpotClient->getContacts($next);
                $contactsData = json_decode($response->getContents());
                $contacts = $contactsData->results;
                
                foreach($contacts as $contact) {
                    $query = $builder->select()->setTable('contacts')->where()->equals('email', 1)->end();
                    $stmt = $pdo->prepare($query);
                    $stmt->bindValue(':v1', $contact->properties->email, PDO::PARAM_STR);
                    $stmt->execute();
                    $db_contact = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if(!$db_contact) {
                        $this->insertLocalHostContact($contact);
                        $inserted++;
                    } else {
                        // $this->updateLocalHostContact();
                    }                    
                }
                if(is_object($contactsData) && property_exists($contactsData, "paging")) {
                    $next = $contactsData->paging->next->link;
                } else {
                    $isLast = true;
                }
            } while (!$isLast);
            return true;
        } catch (ClientException $e) {
            
            $this->processClientException($e, 'Could not list SL contacts');
        }
    }

    /**
     * Method to insert contact in localhost
     *
     * @param array $contact
     * @return bool
     */
    protected function insertLocalHostContact($contact)
    {
        $builder = new MySqlBuilder(); 
        $pdo = new PDO('mysql:host=' . $this->servername . ';dbname='.$this->dbname, $this->username, $this->password);
        $query = $builder->insert()->setTable('contacts')->setValues([
            'account_number' => $contact->properties->account_number,
            'ap_first_name' => $contact->properties->ap_first_name,
            'ap_last_name' => $contact->properties->ap_last_name,
            'ap_phone_number' => $contact->properties->ap_phone_number,
            'applicant_first_name' => $contact->properties->applicant_first_name,
            'applicant_last_name' => $contact->properties->applicant_last_name,
            'branch_id' => $contact->properties->branch_id,
            'campaign_id' => $contact->properties->campaign_id,
            'cloudlink_id' => $contact->properties->cloudlink_id,
            'customer_no' => $contact->properties->customer_no,
            'date_of_birth' => $contact->properties->date_of_birth,
            'division' => $contact->properties->division,
            'division_id' => $contact->properties->division_id,
            'employee_id' => $contact->properties->employee_id,
            'gender' => $contact->properties->gender,
            'ip_city' => $contact->properties->ip_city,
            'ip_country' => $contact->properties->ip_country,
            'ip_country_code' => $contact->properties->ip_country_code,
            'ip_state' => $contact->properties->ip_state,
            'ip_state_code' => $contact->properties->ip_state_code,
            'ip_zipcode' => $contact->properties->ip_zipcode,
            'lastmodifieddate' => $contact->properties->lastmodifieddate,
            'location' => $contact->properties->location,
            'serial_number' => $contact->properties->serial_number,
            'stage_id' => $contact->properties->stage_id,
            'firstname' => $contact->properties->firstname,
            'lastname' => $contact->properties->lastname,
            'email' => $contact->properties->email,
            'mobilephone' => $contact->properties->mobilephone,
            'phone' => $contact->properties->phone,
            'fax' => $contact->properties->fax,
            'address' => $contact->properties->address,
            'owneremail' => $contact->properties->owneremail,
            'ownername' => $contact->properties->ownername,
            'city' => $contact->properties->city,
            'state' => $contact->properties->state,
            'zip' => $contact->properties->zip,
            'country' => $contact->properties->country,
            'closedate' => $contact->properties->closedate,
            'createdate' => $contact->properties->createdate,
            'company' => $contact->properties->company,
            'website' => $contact->properties->website,
        ]);
        $sql = $builder->writeFormatted($query); 
        $values = $builder->getValues();
        $stmt = $pdo->prepare($query);
        self::PDOBindArray($stmt,$values);
        $stmt->execute();
        $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->logger->info('Inserted HSContact in local: '. $contact->properties->email);
        return true;

    }

    /**
     * Bind PDO values
     *
     * @param mixed $PDO statement
     * @param array $values
     * @return void
     */
    protected function PDOBindArray(&$poStatement, &$paArray) 
    {
        foreach ($paArray as $k=>$v) {
        
            @$poStatement->bindValue($k,$v);
        
        }
    }

    /**
     * Utility method for parsing and logging a Guzzle ClientException
     *
     * @param  mixed $e
     * @param  mixed $message
     * @param  mixed $andThrow
     * @return void
     */
    protected function processClientException($e, $message, $andThrow = true)
    {
        $errResponse = $e->getResponse();
        $errJson = json_decode($errResponse->getBody()->getContents());

        $this->logger->error('HubSpotToLocalHost: ' . $message .':', [
            'response' => $errJson,
        ]);

        if ($andThrow) {
            throw $e;
        }
    }
}
