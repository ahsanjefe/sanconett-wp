<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core\Integrations\SalesLink;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Monolog\Logger;
use Snapshot\HubspotToSaleslink\Core\Config;
use Snapshot\HubspotToSaleslink\Core\Integrations\SalesLink\SalesLinkApiEnvironment;

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

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;

        $this->client = new Client([
            'base_uri' => Config::getCloudLinkApiBaseUri(),
        ]);
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
        if (static::$accessTokenExpiration == null || time() > static::$accessTokenExpiration) {
            $this->authenticate();
        }

        if (!$options) {
            $options['headers'] = [];
        }

        $options['headers']['Tenant'] = 'hawthorne';
        $options['headers']['Authorization'] = "Bearer " . static::$accessToken;
        $options['headers']['x-api-key'] = Config::getCloudLinkApiKey();

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
    protected function authenticate()
    {
        $response = $this->client->post('auth', [
            'headers' => [
                'Tenant' => 'hawthorne',
                'x-api-key' => Config::getCloudLinkApiKey(),
            ],
        ]);

        $json = json_decode($response->getBody()->getContents());

        static::$accessToken = $json->access_token;
        static::$accessTokenExpiration = time() + $json->expires_in;

        $this->logger->info('SalesLinkApiClient: Got access token', [
            'token' => static::$accessToken,
            'expiresIn' => static::$accessTokenExpiration,
        ]);
    }

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
}
