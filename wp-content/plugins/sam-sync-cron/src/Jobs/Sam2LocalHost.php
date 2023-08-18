<?php

/**
 * @package cloudlink-to-localhost
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

class Sam2LocalHost
{
    protected const CRON_HOOK = 'Sam2_local_host';
    protected const SCHEDULE_NAME = 'Sam2_local_host_sch';

    /**
     * Registers a WordPress Cron Task
     *
     * @return void
     */
    public static function init()
    {
      
        $logger = LoggerFactory::make('SamToLocalHost');
        $instance = new Sam2LocalHost($logger);

        add_filter('cron_schedules', function ($schedules) {
            $schedules[static::SCHEDULE_NAME] = [
                'interval' => 14400,
                'display' => __('Sam2local: opportunities sync'),
            ];

            return $schedules;
        });

        add_action(static::CRON_HOOK, function () use ($instance) {
            $instance->execute();
        });

        if (! wp_next_scheduled(static::CRON_HOOK)) {
            $logger->info('SamToLocalHost: Scheduled job!');
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
        $logger = LoggerFactory::make('Sam2LocalHost');
        $timestamp = wp_next_scheduled(static::CRON_HOOK);
        wp_unschedule_event($timestamp, static::CRON_HOOK);

        $logger->info('Sam2LocalHost: Unregistered job!');
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
        // $this->hubSpotClient = HubSpotApiClient::make($logger);
        $this->servername = DB_HOST;
        $this->username = DB_USER;
        $this->password = DB_PASSWORD;
        $this->dbname = DB_NAME;
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
            $this->logger->info('I am here:');
            var_dump('die');die();
            // 1. Update local contacts from cloudlinks
            $updatedContacts = $this->fetchSamOpportunities();

            $this->logger->info('Sam2LocalHost: updated ' . $updatedContacts . ' contacts in local', [
                'opps' => $updatedContacts,
            ]);

            if ($updatedContacts === 0) {
                return false;
            }

            $this->logger->info('Sam2LocalHost: Job complete!', [
                'success' => 'Cloudlink to hubspot updated successfully'
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Sam2LocalHost: Could not complete execution:');
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

        $this->logger->info('Sam2LocalHost: Executing...');
    }

    /**
     * Loads opportunities from SalesLink
     *
     * @return void
     */
    protected function fetchSamOpportunities()
    {
        $builder = new GenericBuilder(); 
        $pdo = new PDO('mysql:host=' . $this->servername . ';dbname='.$this->dbname, $this->username, $this->password);
        try {
            $isLast = false;
            $currentPage = 0;
            $updated = 0;
            do {
                $toUpdate = [];
                $response = $this->salesLinkClient->getOpportunities(1000, '01/01/2013', '12/31/2013');
                foreach($response->opportunitiesData as $data) {
                    $this->logger->info('$noticeId' . $data['noticeId']);
                }
                dd('dumpppppp contr');
                $contactsData = json_decode($response->getBody()->getContents());
                $contacts = $contactsData->content;
                
                $pagination = $contactsData->pagination;
                $isLast = $pagination->last;
                
                foreach($contacts as $contact) {
                    
                    if(trim($contact->email)) {
                        $query = $builder->select()->setTable('contacts')->where()->equals('email', 1)->end();
                        $stmt = $pdo->prepare($query);
                        $stmt->bindValue(':v1', $contact->email, PDO::PARAM_STR);
                        $stmt->execute();
                        $db_contact = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if($db_contact) {
                            $names = explode(" ", $contact->name);
                            if(count($names) > 1) {
                                $lastname = array_pop($names);
                                $firstname = implode(" ", $names);
                            }
                            else {
                                $firstname = $contact->name;
                                $lastname = "";
                            }
                            $query = $builder->update()->setTable('contacts')
                                ->setValues([
                                    'cloudlink_id' => $contact->id . '_' . $contact->customerId,
                                    'customer_no' => $contact->customerId,
                                    'division' => $contact->mainDivision,
                                    'lastmodifieddate' => $contact->UpdatedDate,
                                    'firstname' => $firstname,
                                    'lastname' => $lastname,
                                    'email' => $contact->email,
                                    'mobilephone' => $contact->mobilePhone,
                                    'phone' => $contact->phone,
                                    'fax' => $contact->fax,
                                    'address' => $contact->address1,
                                    'city' => $contact->city,
                                    'state' => $contact->state,
                                    'zip' => $contact->zipCode,
                                    'createdate' => $contact->CreatedDate,
                                ])->where()->equals('email', $contact->email)->end();
                            $sql = $builder->writeFormatted($query); 
                            $values = $builder->getValues();
                            $stmt = $pdo->prepare($query);
                            self::PDOBindArray($stmt,$values);
                            $stmt->execute();
                            $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $this->logger->info('updated in local: '. $contact->email);
                            $updated++;
                        }
                    }
                }
                $currentPage++;
            } while (!$isLast);

            return $updated;
        } catch (ClientException $e) {
            $this->processClientException($e, 'Could not list SL contacts');
        }
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

        $this->logger->error('Sam2LocalHost: ' . $message .':', [
            'response' => $errJson,
        ]);

        if ($andThrow) {
            throw $e;
        }
    }
}
