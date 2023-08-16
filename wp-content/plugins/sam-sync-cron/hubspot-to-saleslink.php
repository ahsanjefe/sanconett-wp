<?php
/**
 * @package sam-to-localhost
 */

/*
 * Plugin Name: Sam Sync
 * Description: Custom integration for sam to localhost.
 * Version: 1.0.0
 * Author: Sanconett
 * Author URI: https://www.sanconett.com/
 * Text Domain: sam-to-localhost
*/

defined( 'ABSPATH' ) or die( 'Nothing to see here.' );


// Use composer for autoloading...
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) )
{
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}


use Snapshot\SamSync\Core\Activate;
use Snapshot\SamSync\Core\Deactivate;
use Snapshot\SamSync\Core\Plugin;
use Snapshot\SamSync\Core\WebhookValidationType;

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