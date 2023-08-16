<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core\Utils;

use WP_REST_Request;
use SevenShores\Hubspot\Utils\Webhooks as HubSpotWebhookUtils;
use Snapshot\HubspotToSaleslink\Core\Config;

/**
 * Utility class to validate a HubSpot request
 */
class HubSpotRequestValidator
{
    /**
     * Validate webhook request by HubSpot v2 token
     *
     * @param  mixed $request
     * @return bool
     */
    public static function validateByHubSpotV2Token(WP_REST_Request $request): bool
    {
        $signatureHeaderName = 'X-Hubspot-Signature';
        $signature = $request->get_header($signatureHeaderName);

        if (!$signature) {
            return false;
        }

        $appClientSecret = Config::getAppClientSecret();
        $method = $request->get_method();
        $uri = 'https://'.$request->get_header('host')."/wp-json".$request->get_route();

        $secret = $appClientSecret.$method.$uri;

        return HubSpotWebhookUtils::isHubspotSignatureValid($signature, $secret, $request->get_body());
    }

    /**
     * Validate request by shared webhook secret
     *
     * @param  WP_REST_Request $request
     * @return bool
     */
    public static function validateBySecretKey(WP_REST_Request $request): bool
    {
        $providedSecret = $request->get_param('key');
        $webhookSecret = Config::getWebhookSecret();

        return $providedSecret == $webhookSecret;
    }
}
