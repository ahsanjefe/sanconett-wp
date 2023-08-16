<?php
/**
 * @package sam-to-localhost
 */

namespace Snapshot\SamSync\Core\Integrations\HubSpot;

use SevenShores\Hubspot\Factory;
use SevenShores\Hubspot\Http\Client;
use GuzzleHttp\Client as GuzzleClient;
use SevenShores\Hubspot\Resources\CrmAssociations;
use Monolog\Logger;
use Snapshot\SamSync\Core\Config;

/**
 * API Client for interacting with HubSpot's API
 */
class HubSpotApiClient
{
    public static function make(Logger $logger)
    {
        return new HubSpotApiClient($logger);
    }

    protected Logger $logger;
    protected Factory $client;
    protected GuzzleClient $http;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->client = Factory::createWithOAuth2Token(Config::getOauthToken());
        $this->http = new GuzzleClient();
        
    }

    /**
     * Get a single HubSpot Deal by ID
     *
     * @param  int $dealId
     * @return object
     */
    public function getDeal(int $dealId)
    {
        return $this->client->deals()->getById($dealId)->data;
    }

    /**
     * Update a single HubSpot Deal by ID
     *
     * @param  int $dealId
     * @param  array $properties
     * @return object
     */
    public function updateDeal(int $dealId, array $properties)
    {
        return $this->client->deals()->update($dealId, $properties)->data;
    }

    /**
     * Loads all Contact records associated with a Deal
     *
     * @param  int $dealId
     * @return array
     */
    public function getDealContacts(int $dealId)
    {
        $associationResponse = $this->client->crmAssociations()->get($dealId, CrmAssociations::DEAL_TO_CONTACT);
        $contactsResponse = $this->client->contacts()->getBatchByIds($associationResponse->data->results);

        // Convert from object to array (raw value is keyed by contact ID)
        $contacts = array_map(function ($row) use ($contactsResponse) {
            return $contactsResponse->data->{$row};
        }, array_keys(get_object_vars($contactsResponse->data)));

        return $contacts;
    }

    /**
     * Loads all Company records associated with a Deal
     *
     * @param  int $dealId
     * @return array
     */
    public function getDealCompanies(int $dealId)
    {
        $companies = [];
        $associationResponse = $this->client->crmAssociations()->get($dealId, CrmAssociations::DEAL_TO_COMPANY);

        foreach ($associationResponse->data->results as $companyId) {
            $companyResponse = $this->client->companies()->getById($companyId);
            array_push($companies, $companyResponse->data);
        }

        return $companies;
    }

    /**
     * Loads all Engagement records associated with a Deal
     *
     * @param  int $dealId
     * @return array
     */
    public function getDealNotes(int $dealId)
    {
        $notes = [];
        $associationResponse = $this->client->crmAssociations()->get($dealId, CrmAssociations::DEAL_TO_ENGAGEMENT);

        foreach ($associationResponse->data->results as $noteId) {
            $noteResponse = $this->client->engagements()->get($noteId);
            array_push($notes, $noteResponse->data);
        }

        return $notes;
    }

    /**
     * Updates cloulink_id to make connection between cloudLinks Hubspot
     *
     * @param  int $cloulinkId
     * @return array
     */
    public function searchContactByEmail(string $email)
    {
        $endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts/search';
        $options = [
            "filterGroups" => [
              [
                "filters" => [
                  [
                    "propertyName" => "email",
                    "operator" => "EQ",
                    "value" => $email
                  ]
                ]
              ]
            ]
        ];
        
        $response = $this->http->request('POST', $endpoint, [
            'headers' => ['Authorization' => 'Bearer ' . Config::getOauthToken(), 'Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($options)
        ])->getBody();
        
        return $response;
    }

    /**
     * get batch contacts
     *
     * @param  array $emails
     * @return array
     */
    public function getBatchContactByEmail(array $emails)
    {
        $endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts/batch/read?archived=false';
        $options = [
            "inputs" => $emails,
            "properties" => [
                "email","cloudlink_id","streetaddress","city","createdate","customer_id","title",
                "description","email","fax","firstname","lastname","phone","address","streetaddress",
                "hs_object_id","division","mobilephone","phone","state","title","description",
                "lastmodifieddate","postalCode"
            ],
            "idProperty" => "email"
        ];
        
        $response = $this->http->request('POST', $endpoint, [
            'headers' => ['Authorization' => 'Bearer ' . Config::getOauthToken(), 'Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($options)
        ])->getBody();
        
        return $response;
    }
/**
     * get batch contacts
     *
     * @param  array $emails
     * @return array
     */
    public function getHubSpotContactByList(array $emails)
    {
        $endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts/batch/read?archived=false';
        $options = [
            "inputs" => $emails,
            "properties" => [
                "email","oudlink_id","streetaddress","city","createdate","customer_no","title",
                "description","email","fax","firstname","lastname","phone","address","streetaddress",
                "hs_object_id","division","mobilephone","phone","state","title","description",
                "lastmodifieddate","postalCode"
            ],
            "idProperty" => "email"
        ];
        
        $response = $this->http->request('POST', $endpoint, [
            'headers' => ['Authorization' => 'Bearer ' . Config::getOauthToken(), 'Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($options)
        ])->getBody();
        
        return $response;
    }
    /**
     * Updates cloulink_id to make connection between cloudLinks Hubspot
     *
     * @param  int $id, string $cloulinkId
     * @return array
     */
    public function updateCloudlinkID(int $id, string $cloulinkId)
    {
        $endpoint = "https://api.hubapi.com/crm/v3/objects/contacts/{$id}";
        $body = [
            "properties" => [
                "cloudlink_id" => $cloulinkId
            ]
        ];

        $this->logger->info('SalesLinkApiClient: Updating contact against hs_object_id ' . $id . ' and adding cloudlink_id ' . $cloulinkId);

        $response = $this->http->request('PATCH', $endpoint, [
            'headers' => ['Authorization' => 'Bearer ' . Config::getOauthToken(), 'Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($body)
        ])->getBody();

        $this->logger->info('Updated successfully');
        return $response;
    }

    public function updateHSContactById(int $id, string $cloulinkId, $contact)
    {
        $names = explode(" ", $contact->name);
        if(count($names) > 1) {
            $lastname = array_pop($names);
            $firstname = implode(" ", $names);
        }
        else {
            $firstname = $contact->name;
            $lastname = "";
        }
        $endpoint = "https://api.hubapi.com/crm/v3/objects/contacts/{$id}";
        $body = [
            "properties" => [
                "cloudlink_id" => $cloulinkId,
                "address" => $contact->address1,
                "city" => $contact->city,
                "cloudlink_id"=> $cloulinkId,
                "division" => $contact->mainDivision,
                "fax" => $contact->fax,
                "firstname" => $firstname,
                "lastmodifieddate" => $contact->UpdatedDate,
                "lastname" => $lastname,
                "mobilephone" => $contact->mobilePhone,
                "phone" => $contact->phone,
                "state" => $contact->state
            ]
        ];

        $this->logger->info('SalesLinkApiClient: Updating contact data against hs_object_id ' . $id . ' and adding cloudlink_id ' . $cloulinkId);

        $response = $this->http->request('PATCH', $endpoint, [
            'headers' => ['Authorization' => 'Bearer ' . Config::getOauthToken(), 'Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($body)
        ])->getBody();

        $this->logger->info('Updated successfully');
        return $response;
    }

    public function updateHSBatchContacts(array $toUpdateContacts, array $clContacts)
    {
        $endpoint = "https://api.hubapi.com/crm/v3/objects/contacts/batch/update";
        $body = [
            "inputs" => $toUpdateContacts
        ];

        $this->logger->info('SalesLinkApiClient: Updating batch contact data');

        $response = $this->http->request('PATCH', $endpoint, [
            'headers' => ['Authorization' => 'Bearer ' . Config::getOauthToken(), 'Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($body)
        ])->getBody();

        $this->logger->info('Updated batch successfully');
        return $response;
    }

        /**
     * Updates cloulink_id to make connection between cloudLinks Hubspot
     *
     * @param  int $cloulinkId
     * @return array
     */
    public function searchCompanyByName(string $name)
    {
        $endpoint = 'https://api.hubapi.com/crm/v3/objects/companies/search';
        $options = [
            "filterGroups" => [
                [
                    "filters" => [
                        [
                            "operator" => "EQ",
                            "propertyName" => "name",
                            "value" => "Brand Featured"
                        ]
                    ]
                ]
            ],
            "properties" => [
                "name",
                "about_us"
            ]
        ];
        
        $response = $this->http->request('POST', $endpoint, [
            'headers' => ['Authorization' => 'Bearer ' . Config::getOauthToken(), 'Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($options)
        ])->getBody();

        // var_dump($response);die();

        
        return $response;
    }

    /**
     * get contacts list from hubSpot
     *
     * @param  array $next
     * @return array
     */
    public function getContacts($next)
    {
        if($next != 'false'){
            $endpoint = $next;
        }else{
            $endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts?properties=email,firstname,lastname,account_number,ap_first_name,ap_last_name,ap_phone_number,applicant_first_name,applicant_last_name,branch_id,campaign_id,cloudlink_id,customer_no,date_of_birth,division,division_id,employee_id,gender,ip_city,ip_country,ip_country_code,ip_state,ip_state_code,ip_zipcode,lastmodifieddate,location,serial_number,stage_id,mobilephone,phone,fax,address,owneremail,ownername,city,state,zip,country,closedate,createdate,company,website&archived=false&limit=100';
        }
               
        $response = $this->http->request('GET', $endpoint, [
            'headers' => ['Authorization' => 'Bearer ' . Config::getOauthToken(), 'Content-Type' => 'application/json', 'Accept' => 'application/json']
        ])->getBody();
    
        return $response;
    }
}
