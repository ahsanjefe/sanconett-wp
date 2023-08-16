<?php
/**
 * @package sam-to-localhost
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

class SyncSalesLinkChangesToHubSpot
{
    protected const CRON_HOOK = 'hs2sl_cron_hook';
    protected const SCHEDULE_NAME = 'hs2sl_five_minutes';

    /**
     * Registers a WordPress Cron Task
     *
     * @return void
     */
    public static function init()
    {
        $logger = LoggerFactory::make('jobs');
        $instance = new SyncSalesLinkChangesToHubSpot($logger);

        add_filter('cron_schedules', function ($schedules) {
            $schedules[static::SCHEDULE_NAME] = [
                'interval' => 300,
                'display' => __('HS2SL: Every Five Minutes'),
            ];

            return $schedules;
        });

        add_action(static::CRON_HOOK, function () use ($instance) {
            $instance->execute();
        });

        if (! wp_next_scheduled(static::CRON_HOOK)) {
            $logger->info('SyncSalesLinkChangesToHubSpot: Scheduled job!');
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
        $logger = LoggerFactory::make('jobs');
        $timestamp = wp_next_scheduled(static::CRON_HOOK);
        wp_unschedule_event($timestamp, static::CRON_HOOK);

        $logger->info('SyncSalesLinkChangesToHubSpot: Unregistered job!');
    }

    protected Logger $logger;
    protected SalesLinkApiClient $salesLinkClient;
    protected HubSpotApiClient $hubSpotClient;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->salesLinkClient = SalesLinkApiClient::make($logger);
        $this->hubSpotClient = HubSpotApiClient::make($logger);
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

            // 1. Retrieve updated Opportunities from SalesLink
            $opportunities = $this->fetchOpportunities();

            // 2. Validate Opportunity data
            $validOpportunities = $this->filterInvalidOpportunities($opportunities);
            $numOpportunities = count($validOpportunities);

            $this->logger->info('SyncSalesLinkChangesToHubSpot: Got ' . $numOpportunities . ' opportunities to send to HubSpot', [
                'opps' => $validOpportunities,
            ]);

            if ($numOpportunities === 0) {
                return false;
            }

            // 3. Try to update each HubSpot Deal matching an Opportunity
            $success = $this->updateHubSpotDeals($validOpportunities);

            $this->logger->info('SyncSalesLinkChangesToHubSpot: Job complete!', [
                'success' => $success
            ]);

            return $success;
        } catch (\Exception $e) {
            $this->logger->error('SyncSalesLinkChangesToHubSpot: Could not complete execution:');
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

        $this->logger->info('SyncSalesLinkChangesToHubSpot: Executing...');
    }

    /**
     * Loads opportunities from SalesLink
     *
     * @return void
     */
    protected function fetchOpportunities()
    {
        try {
            $opportunitiesResponse = $this->salesLinkClient->getLatestOpportunities();
            $opportunities = json_decode($opportunitiesResponse->getBody()->getContents())->content;

            return $opportunities;
        } catch (ClientException $e) {
            $this->processClientException($e, 'Could not list SL opportunities');
        }
    }

    /**
     * Removes invalid SalesLink Opportunities from an array
     *
     * @param  mixed $opportunities
     * @param  int $threshold
     * @return void
     */
    protected function filterInvalidOpportunities($opportunities, int $threshold = 10)
    {
        // We only care about opportunities that have a HubSpot ID
        $validOpportunities = array_filter($opportunities, function ($o) {
            return isset($o->externalReferenceNumber);
        });

        /**
         * When we send data from HubSpot to SalesLink, the data is written in
         * multiple operations and each changes the updated timestamp. So, we
         * have a threshold of seconds, and if the last update is greater than
         * N seconds from creation, then we consider it valid for writing to
         * HubSpot.
         */
        $updatedOpportunities = array_filter($validOpportunities, function ($o) {
            $created = Carbon::parse($o->enterDate);
            $updated = Carbon::parse($o->changeDate);

            $diffInSeconds = $created->diffInSeconds($updated);

            return $diffInSeconds > $threshold;
        });

        return $updatedOpportunities;
    }

    /**
     * Update a list of HubSpot Deals related to SalesLink Opportunities
     *
     * @param  mixed $opportunities
     * @return void
     */
    protected function updateHubSpotDeals($opportunities)
    {
        $success = true;

        foreach ($opportunities as $opportunity) {
            try {
                $this->updateHubSpotDealFromOpportunity($opportunity);
            } catch (ClientException $e) {
                $this->processClientException($e, 'Could not update HS deal', false);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Update a single HubSpot Deal related to a SalesLink Opportunity
     *
     * @param  mixed $opportunity
     * @return void
     */
    protected function updateHubSpotDealFromOpportunity($opportunity)
    {
        $dealId = intval($opportunity->externalReferenceNumber);
        $hsDeal = $this->hubSpotClient->getDeal($dealId);

        if (!$hsDeal) {
            $this->logger->info('SyncSalesLinkChangesToHubSpot: No deal found with id ' . $dealId);
            return;
        }

        // Update Deal properties from Opportunity data
        $properties = SalesLinkApiUtils::salesLinkOpportunityToHubSpotDealProperties($opportunity, $hsDeal);

        $this->logger->info('SyncSalesLinkChangesToHubSpot: Updating deal ' . $dealId, [
            'properties' => $properties,
            // 'deal' => $hsDeal, // Lots of data here
        ]);

        $updateResponse = $this->hubSpotClient->updateDeal($dealId, $properties);

        $this->logger->info('SyncSalesLinkChangesToHubSpot: Deal updated ' . $dealId, [
            // 'updated' => $updateResponse, // Lots of data here
        ]);
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

        $this->logger->error('SyncSalesLinkChangesToHubSpot: ' . $message .':', [
            'response' => $errJson,
        ]);

        if ($andThrow) {
            throw $e;
        }
    }
}
