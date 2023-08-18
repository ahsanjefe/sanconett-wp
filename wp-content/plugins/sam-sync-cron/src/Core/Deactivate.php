<?php
/**
 * @package sam-to-localhost
 */

namespace Snapshot\SamSync\Core;

use Snapshot\SamSync\Jobs\SyncSalesLinkChangesToHubSpot;
use Snapshot\SamSync\Jobs\HubSpotToLocalHost;
use Snapshot\SamSync\Jobs\CloudlinkToLocalHost;
use Snapshot\SamSync\Jobs\Sam2LocalHost;

class Deactivate
{
    public static function run()
    {
        flush_rewrite_rules();

        // SyncSalesLinkChangesToHubSpot::destroy();
        // HubSpotToLocalHost::destroy();
        // CloudlinkToLocalHost::destroy();
        Sam2LocalHost::destroy();
    }
}
