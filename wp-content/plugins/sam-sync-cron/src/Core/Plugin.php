<?php
/**
 * @package sam-to-localhost
 */

namespace Snapshot\SamSync\Core;

use Snapshot\SamSync\Core\Config;
use Snapshot\SamSync\Core\WebhookValidationType;
use Snapshot\SamSync\Http\HubSpotDealWebhookController;
use Snapshot\SamSync\Jobs\SyncSalesLinkChangesToHubSpot;
use Snapshot\SamSync\Jobs\CloudlinkToLocalHost;
use Snapshot\SamSync\Jobs\HubSpotToLocalHost;

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
