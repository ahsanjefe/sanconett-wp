<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\SamSync\Core\Integrations\SalesLink;

use Carbon\Carbon;
use Monolog\Logger;
use GuzzleHttp\Client;
use Snapshot\SamSync\Core\Config;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Exception\ConnectException;
// use Snapshot\SamSync\Core\Integrations\SalesLink\SalesLinkApiEnvironment;

/**
 * API Client for interacting with SalesLink's API
 */
class SalesLinkApiClient
{
    public static ?string $accessToken = null;
    public static ?int $accessTokenExpiration = null;

    public static function make(Logger $logger)
    {
        return new SalesLinkApiClient($logger);
    }

    protected Logger $logger;
    protected Client $client;
    protected $api_key;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;

        $this->client = new Client([
            'verify'  => 'C:\xampp8.2\php\cacert.pem',
            'timeout'  => 2.0,
        ]);
        // $this->api_key = Config::getApiKey();
    }

    /**
     * Perform GET request at a URI
     *
     * @param  string $uri
     * @param  mixed $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function get(string $uri, $options = [])
    {
        $this->logger->info('inside get');
        $this->logger->info('$uri ' . $uri);
        // $this->logger->info('$options ' , $options);
        return $this->request('GET', $uri, $options);
    }

    /**
     * Perform POST request at a URI
     *
     * @param  string $uri
     * @param  mixed $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function post(string $uri, $options = [])
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * Perform a request at a URI. This method also automatically authenticates.
     *
     * @param  string $uri
     * @param  mixed $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function request(string $method, string $uri, $options)
    {
        // if (static::$accessTokenExpiration == null || time() > static::$accessTokenExpiration) {
        //     $this->authenticate();
        // }

        // if (!$options) {
        //     $options['headers'] = [];
        // }

        // $options['headers']['Tenant'] = 'hawthorne';
        // $options['headers']['Authorization'] = "Bearer " . static::$accessToken;
        // $options['headers']['api_key'] = Config::getCloudLinkApiKey();

        return $this->client->request($method, $uri, $options);
    }

    /**
     * Creates a SalesLink Opportunity
     *
     * @param  mixed $opportunity
     * @return void
     */
    public function createOpportunity($opportunity)
    {
        return $this->post('Opportunities', [
            "json" => $opportunity,
        ]);
    }

    /**
     * Retrives latest SalesLink Opportunities
     */
    public function getLatestOpportunities($since = null)
    {
        if (!$since) {
            $since = Carbon::now()->setTimezone('America/Los_Angeles')->subMinutes(5)->toDateTimeLocalString();
        }

        $this->logger->info('SalesLinkApiClient: Getting opportunities since ' . $since);

        return $this->get('Opportunities?sort=-id&filter[changeDate][gte]=' . $since);
    }

    /**
     * Retrieves and stores authentication data for SalesLink
     *
     * @return void
     */
    // protected function authenticate()
    // {
    //     $response = $this->client->post('auth', [
    //         'headers' => [
    //             'Tenant' => 'hawthorne',
    //             'x-api-key' => Config::getCloudLinkApiKey(),
    //         ],
    //     ]);

    //     $json = json_decode($response->getBody()->getContents());

    //     static::$accessToken = $json->access_token;
    //     static::$accessTokenExpiration = time() + $json->expires_in;

    //     $this->logger->info('SalesLinkApiClient: Got access token', [
    //         'token' => static::$accessToken,
    //         'expiresIn' => static::$accessTokenExpiration,
    //     ]);
    // }

    /**
     * Retrives all SalesLink contacts
     */
    public function getContacts($page = null)
    {
        $this->logger->info('SalesLinkApiClient: Getting contacts for page ' . $page);

        return $this->get('Contacts/?size=100&sort=CreatedDate,desc&page=' . $page, []);
    }

    /**
     * Retrives a SalesLink contact by email
     */
    public function getContactByEmail($email = null)
    {
        $this->logger->info('SalesLinkApiClient: Getting contact for email ' . $email);

        return $this->get('Contacts/?email=' . $email, []);
    }

    /**
     * Retrives a SalesLink contact count by email
     */
    public function getContactCountByEmail($email = null)
    {

        $this->logger->info('SalesLinkApiClient: Getting contact count for email ' . $email);

        return $this->get('Contacts/count?email=' . $email, []);
    }

    /**
     * Retrives all SalesLink companies
     */
    public function getCompanies($page = null)
    {
        $this->logger->info('SalesLinkApiClient: Getting companies for page ' . $page);

        return $this->get('Customers?size=100&&page=' . $page, []);
    }

    /**
     * Retrives all SalesLink companies
     */
    public function getOpportunities($limit = null, $postedFrom = null, $postedTo = null, ) 
    {
        $this->logger->info('SamSyncCron: Getting opportunities from ' . $postedFrom . ' to ' . $postedTo);
        $this->logger->info('$limit ' . $limit);
        $this->logger->info('$postedFrom ' . $postedFrom);
        $this->logger->info('$postedTo ' . $postedTo);

        // $request = new \GuzzleHttp\Psr7\Request('GET', 'https://api.sam.gov/opportunities/v2/search?limit='.$limit.'&postedFrom='.$postedFrom.'&postedTo='.$postedTo.'&api_key=VuHsvoNnugurrt9kgwhf5pL5bJ53ykImUqeGZQDa', [
        //     'Host' => 'api.sam.gov',
        //     'Cookies' => 'citrix_ns_id=S6GYCBaL6EiGZ1cQHaXzV8+j5f80003'
        //   ]);

        // $check = (new Client())->send($request, [
        //     'debug' => true,
        //     'Cookies' => 'citrix_ns_id=S6GYCBaL6EiGZ1cQHaXzV8+j5f80003'
        // ]);

        // $client = new Client();
        // $headers = [
        // 'Cookie' => 'citrix_ns_id=S6GYCBaL6EiGZ1cQHaXzV8+j5f80003'
        // ];
        // $request = new \GuzzleHttp\Psr7\Request('GET', 'https://api.sam.gov/opportunities/v2/search?api_key=VuHsvoNnugurrt9kgwhf5pL5bJ53ykImUqeGZQDa&limit=1000&postedFrom=01/01/2013&postedTo=12/31/2013', $headers);
        // $check = $client->sendAsync($request)->wait();
        
        // $check = $this->client->request('https://api.sam.gov/opportunities/v2/search?limit='.$limit.'&postedFrom='.$postedFrom.'&postedTo='.$postedTo.'&api_key=VuHsvoNnugurrt9kgwhf5pL5bJ53ykImUqeGZQDa', [
        try {
            // $jar = new \GuzzleHttp\Cookie\CookieJar();
            // $check = $this->client->request('GET', "http://api.sam.gov/opportunities/v2/search?limit=1000&postedFrom=01/01/2013&postedTo=12/31/2013&api_key=VuHsvoNnugurrt9kgwhf5pL5bJ53ykImUqeGZQDa", [
            //     'verify'  => 'C:\xampp8.2\php\cacert.pem',
            //     'allow_redirects' => true,
            //     'cookies' => true,
            //     'connect_timeout' => 10,
            //     'debug' => true,
            //     // 'force_ip_resolve' => 'v6',
            //     'cookies' => $jar,
            //     'headers' => [
            //         'Accept' => 'application/json',
            //         'Host'  => 'https://api.sam.gov',
            //         'api_key' => 'VuHsvoNnugurrt9kgwhf5pL5bJ53ykImUqeGZQDa',
            //         'Cookie' => 'citrix_ns_id=h6Ar+Kh5BVjErQqC0WUdHqeB9wY0003',
            //     ],
            //     'proxy' => 'http://localhost:80',
                
            // ]);
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sam.gov/prod/opportunities/v2/search?noticeid=c6c00cd76e7740fcbfe03272458a8007&limit=1&api_key=VuHsvoNnugurrt9kgwhf5pL5bJ53ykImUqeGZQDa',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'api_key: VuHsvoNnugurrt9kgwhf5pL5bJ53ykImUqeGZQDa',
                'Cookie: citrix_ns_id=h6Ar+Kh5BVjErQqC0WUdHqeB9wY0003'
            ),
            ));

            $check = curl_exec($curl);
            
            curl_close($curl);

            return $check;
            // dd($check);

            // $status = $check->getStatusCode();
            // $body   = $check->getBody()->getContents();

            // $this->logger->info('check ' . $check);
            // $this->logger->info('$body' . $body);
            
        } catch ( ConnectException $e ) {
            $error = [
                'status' => 404,
                'body'   => $e->getMessage(),
            ];
            
            $this->logger->info('$error' . $e->getMessage());
            // return $error;
        } 
        // 'verify' => false,
        // 'headers' => [
        //     'Accept' => 'application/json',
        //     'Host' => 'api.sam.gov'
        //     'Cookie' => 'api.sam.gov'
        //     ]
        
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://api.sam.gov/opportunities/v2/search?api_key=VuHsvoNnugurrt9kgwhf5pL5bJ53ykImUqeGZQDa&limit=1000&postedFrom=01%2F01%2F2013&postedTo=12%2F31%2F2013',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'GET',
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // $this->logger->info('response' . $check);
        // $this->logger->info('after help');
        // echo $check;

        // echo $check->getBody();
        // return $check;

    }
}
