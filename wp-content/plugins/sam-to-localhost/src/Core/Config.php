<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core;

use Snapshot\HubspotToSaleslink\Core\Exceptions\ConfigValueMissing;
use Snapshot\HubspotToSaleslink\Core\WebhookValidationType;

/**
 * Plugin configuration helper.
 */
class Config
{
    public const APP_ID_KEY = 'HUBSPOT_TO_SALESLINK_APP_ID';
    public const APP_CLIENT_SECRET_KEY = 'HUBSPOT_TO_SALESLINK_APP_CLIENT_SECRET';
    public const HUBSPOT_OAUTH_TOKEN = 'HUBSPOT_TO_SALESLINK_HUBSPOT_OAUTH_TOKEN';
    public const LOG_PATH_KEY = 'HUBSPOT_TO_SALESLINK_LOG_PATH';
    public const WEBHOOK_SECRET = 'HUBSPOT_TO_SALESLINK_WEBHOOK_SECRET';
    public const CLOUDLINK_API_BASE_URI = 'HUBSPOT_TO_SALESLINK_CLOUDLINK_API_BASE_URI';
    public const CLOUDLINK_API_KEY = 'HUBSPOT_TO_SALESLINK_CLOUDLINK_API_KEY';

    /**
     * Validates that required configuration values are set and available.
     *
     * @return void
     */
    public static function init(string $validationType)
    {
        if (!static::getAppId()) {
            throw new ConfigValueMissing(static::APP_ID_KEY);
        }

        if (!static::getOauthToken()) {
            throw new ConfigValueMissing(static::HUBSPOT_OAUTH_TOKEN);
        }

        if ($validationType == WebhookValidationType::HubSpotV2Token) {
            if (!static::getAppClientSecret()) {
                throw new ConfigValueMissing(static::APP_CLIENT_SECRET_KEY);
            }
        }

        if ($validationType == WebhookValidationType::SharedSecret) {
            if (!static::getWebhookSecret()) {
                throw new ConfigValueMissing(static::WEBHOOK_SECRET);
            }
        }

        if (!static::getLogPath()) {
            throw new ConfigValueMissing(static::LOG_PATH_KEY);
        }

        if (!static::getCloudLinkApiBaseUri()) {
            throw new ConfigValueMissing(static::CLOUDLINK_API_BASE_URI);
        }

        if (!static::getCloudLinkApiKey()) {
            throw new ConfigValueMissing(static::CLOUDLINK_API_KEY);
        }
    }

    /**
     * Get the HubSpot App ID
     *
     * @return void
     */
    public static function getAppId()
    {
        return static::get(static::APP_ID_KEY);
    }

    /**
     * Get the HubSpot OAuth token
     *
     * @return void
     */
    public static function getOauthToken()
    {
        return static::get(static::HUBSPOT_OAUTH_TOKEN);
    }

    /**
     * Get the HubSpot App Client Secret
     *
     * @return void
     */
    public static function getAppClientSecret()
    {
        return static::get(static::APP_CLIENT_SECRET_KEY);
    }

    /**
     * Get the shared webhook secret
     *
     * @return void
     */
    public static function getWebhookSecret()
    {
        return static::get(static::WEBHOOK_SECRET);
    }

    /**
     * Get the path to the plugin's logs
     *
     * @return void
     */
    public static function getLogPath()
    {
        return static::get(static::LOG_PATH_KEY);
    }

    /**
     * Get the SalesLink API base URI
     *
     * @return void
     */
    public static function getCloudLinkApiBaseUri()
    {
        return static::get(static::CLOUDLINK_API_BASE_URI);
    }

    /**
     * Get the SalesLink API key
     *
     * @return void
     */
    public static function getCloudLinkApiKey()
    {
        return static::get(static::CLOUDLINK_API_KEY);
    }

    /**
     * Get a configuration value by key
     *
     * @param  string $key
     * @return void
     */
    public static function get(string $key)
    {
        return constant($key) ?? getenv($key);
    }
}
