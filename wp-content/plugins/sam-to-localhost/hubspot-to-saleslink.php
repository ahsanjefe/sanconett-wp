<?php
/**
 * @package hubspot-to-saleslink
 */

/*
 * Plugin Name: Hubspot to SalesLink
 * Description: Custom integration for Hubspot to SalesLink.
 * Version: 1.0.0
 * Author: Snapshot Interactive
 * Author URI: https://www.snapshotinteractive.com/
 * Text Domain: hubspot-to-saleslink
*/

defined( 'ABSPATH' ) or die( 'Nothing to see here.' );


// Use composer for autoloading...
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) )
{
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}


use Snapshot\HubspotToSaleslink\Core\Activate;
use Snapshot\HubspotToSaleslink\Core\Deactivate;
use Snapshot\HubspotToSaleslink\Core\Plugin;
use Snapshot\HubspotToSaleslink\Core\WebhookValidationType;

function activate()
{
    Activate::run();
}
register_activation_hook(__FILE__, 'activate');


function deactivate()
{
    Deactivate::run();
}
register_deactivation_hook(__FILE__, 'deactivate');

Plugin::init(WebhookValidationType::SharedSecret);