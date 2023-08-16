<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core;

use Snapshot\HubspotToSaleslink\Core\Config;
use Snapshot\HubspotToSaleslink\Core\WebhookValidationType;
use Snapshot\HubspotToSaleslink\Http\HubSpotDealWebhookController;
use Snapshot\HubspotToSaleslink\Jobs\SyncSalesLinkChangesToHubSpot;
use Snapshot\HubspotToSaleslink\Jobs\CloudlinkToLocalHost;
use Snapshot\HubspotToSaleslink\Jobs\HubSpotToLocalHost;

/**
 * A class which allows for a single entry-point to bootstrap the plugin
 */
class Plugin
{
    /**
     * Initializes the plugin
     *
     * @return void
     */
    public static function init(string $validationType)
    {
        Config::init($validationType);
        HubSpotDealWebhookController::init($validationType);
        SyncSalesLinkChangesToHubSpot::init();
        CloudlinkToLocalHost::init();
        // HubSpotToLocalHost::init();
    }
}
