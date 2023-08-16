<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core;

/**
 * Allows for choosing of HubSpot webhook request validation method
 */
class WebhookValidationType
{
    public const HubSpotV2Token = "HubSpotV2Token";
    public const SharedSecret = "SharedSecret";
}
