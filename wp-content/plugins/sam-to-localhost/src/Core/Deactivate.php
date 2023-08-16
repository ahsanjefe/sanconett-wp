<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core;

use Snapshot\HubspotToSaleslink\Jobs\SyncSalesLinkChangesToHubSpot;
use Snapshot\HubspotToSaleslink\Jobs\HubSpotToLocalHost;
use Snapshot\HubspotToSaleslink\Jobs\CloudlinkToLocalHost;

class Deactivate
{
    public static function run()
    {
        flush_rewrite_rules();

        SyncSalesLinkChangesToHubSpot::destroy();
        HubSpotToLocalHost::destroy();
        CloudlinkToLocalHost::destroy();
    }
}
