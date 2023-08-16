<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Http;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Snapshot\HubspotToSaleslink\Core\Logging\LoggerFactory;
use Snapshot\HubspotToSaleslink\Core\Config;
use Snapshot\HubspotToSaleslink\Core\WebhookValidationType;
use Snapshot\HubspotToSaleslink\Core\Utils\HubSpotRequestValidator;
use Snapshot\HubspotToSaleslink\Core\Integrations\SalesLink\SalesLinkApiClient;
use Snapshot\HubspotToSaleslink\Core\Integrations\SalesLink\Utils as SalesLinkApiUtils;
use Snapshot\HubspotToSaleslink\Core\Integrations\HubSpot\HubSpotApiClient;

/**
 * Inbound Webhook from HubSpot for new sales deals
 */
class HubSpotDealWebhookController
{
    /**
     * Registers a WordPress API endpoint
     *
     * @return void
     */
    public static function init(string $validationType)
    {
        $logger = LoggerFactory::make('webhooks');
        $instance = new HubSpotDealWebhookController($validationType, $logger);

        add_action('rest_api_init', function () use ($instance) {
            register_rest_route('hs2sl/v1', '/deal', [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$instance, 'handle'],
                'args' => [],
                'permission_callback' => '__return_true',
            ]);
        });
    }

    protected string $validationType;
    protected Logger $logger;
    protected SalesLinkApiClient $salesLinkClient;
    protected HubSpotApiClient $hubSpotClient;

    public function __construct(string $validationType, Logger $logger)
    {
        $this->validationType = $validationType;
        $this->logger = $logger;
        $this->salesLinkClient = SalesLinkApiClient::make($logger);
        $this->hubSpotClient = HubSpotApiClient::make($logger);
    }

    /**
     * Request handler for new HubSpot Webhook API calls
     *
     * @param  mixed $request
     * @return WP_REST_Response
     */
    public function handle(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $this->logRequest($request);

            // Bail out if the request cannot be verified by signature
            if (!$this->validateRequest($request)) {
                return $this->jsonResponse([
                    'message' => 'Request could not be validated.',
                ], 401);
            }

            $this->processRequest($request);

            return $this->jsonResponse([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('HubSpotDealWebhookController: Could not process input request:');
            $this->logger->error($e);

            return $this->jsonResponse([
                'message' => method_exists($e, 'getMessage') ? $e->getMessage() : $e->message,
            ], 500);
        }
    }

    /**
     * Resets the unique request ID and then logs out the request details
     *
     * @param  mixed $request
     * @return void
     */
    protected function logRequest(WP_REST_Request $request): void
    {
        foreach ($this->logger->getProcessors() as $processor) {
            if ($processor instanceof UidProcessor) {
                $processor->reset();
            }
        }

        $this->logger->info('HubSpotDealWebhookController: Inbound request from HubSpot:', [
            'headers' => $request->get_headers(),
            'body' => $request->get_json_params(),
        ]);
    }

    /**
     * Validates that the HubSpot request includes the proper signature.
     *
     * We are expecting to validate the v2 request signature
     *
     * see: https://developers.hubspot.com/docs/api/webhooks/validating-requests
     *
     * @param  mixed $request
     * @return bool
     */
    protected function validateRequest(WP_REST_Request $request): bool
    {
        switch ($this->validationType) {
            case WebhookValidationType::HubSpotV2Token:
                $isValid = HubSpotRequestValidator::validateByHubSpotV2Token($request);
                break;
            case WebhookValidationType::SharedSecret:
            default:
                $isValid = HubSpotRequestValidator::validateBySecretKey($request);
                break;
        }

        $this->logger->info('HubSpotDealWebhookController: Request valid?: ' . ($isValid ? 'true' : 'false'), [
            'key' => $request->get_param('key')
        ]);

        return $isValid;
    }

    /**
     * WIP method for processing the inbound request. We will use this to send data to
     * SalesLink.
     *
     * @param  mixed $request
     * @return void
     */
    protected function processRequest(WP_REST_Request $request): void
    {
        $hsDeal = (object) $request->get_json_params();
        $this->logger->info('HubSpotDealWebhookController: Processing HubSpot Deal with ID:', [
            'id' => $hsDeal->objectId,
        ]);

        $hsDealContacts = $this->hubSpotClient->getDealContacts($hsDeal->objectId);
        $hsDealCompanies = $this->hubSpotClient->getDealCompanies($hsDeal->objectId);
        $hsDealNotes = $this->hubSpotClient->getDealNotes($hsDeal->objectId);

        $deal = new \stdClass();
        $deal->data = json_decode(json_encode($hsDeal));
        $deal->contact = current($hsDealContacts);
        $deal->company = current($hsDealCompanies);
        $deal->notes = json_decode(json_encode($hsDealNotes));

        $this->logger->info('HubSpotDealWebhookController: Processed HubSpot deal:', [
            'deal' => $deal,
        ]);

        $slOpportunity = SalesLinkApiUtils::hubSpotDealToSalesLinkOpportunity($deal);

        $this->logger->info('HubSpotDealWebhookController: Creating SalesLink Opportunity', [
            'opportunity' => $slOpportunity,
        ]);

        try {
            $slOpportunityResponse = $this->salesLinkClient->createOpportunity($slOpportunity);
            $json = json_decode($slOpportunityResponse->getBody()->getContents());
            $this->logger->info('HubSpotDealWebhookController: SalesLink Opportunity Response:', [
                'json' => $json,
            ]);
        } catch (ClientException $e) {
            $errResponse = $e->getResponse();
            $errJson = json_decode($errResponse->getBody()->getContents());

            $this->logger->error('HubSpotDealWebhookController: Could not create SL opportunity:', [
                'response' => $errJson,
            ]);

            throw $e;
        }

        // Mark Deal as synced
        $this->hubSpotClient->updateDeal($hsDeal->objectId, [
            [
                'name' => 'last_synced_from_saleslink',
                'value' => Carbon::now()->toISOString(),
            ],
        ]);

        $this->logger->info('HubSpotDealWebhookController: marked Deal ' . $hsDeal->objectId . ' as synced.');
    }

    /**
     * Utility method for returning a JSON WP response
     *
     * @param  array $body
     * @param  int $status
     * @return WP_REST_Response
     */
    protected function jsonResponse(array $body, int $status = 200): WP_REST_Response
    {
        return new WP_REST_Response($body, $status, [
            'Content-Type' => 'application/json'
        ]);
    }
}
