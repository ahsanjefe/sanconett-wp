<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core;

class Activate
{
    public static function run()
    {
        $version = get_option('hubspot_to_saleslink_version', '1.0');

        flush_rewrite_rules();
    }
}
