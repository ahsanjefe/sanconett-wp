<?php
/**
 * @package sam-to-localhost
 */

namespace Snapshot\SamSync\Core;

/**
 * Allows for choosing of HubSpot webhook request validation method
 */
class WebhookValidationType
{
    public const HubSpotV2Token = "HubSpotV2Token";
    public const SharedSecret = "SharedSecret";
}
