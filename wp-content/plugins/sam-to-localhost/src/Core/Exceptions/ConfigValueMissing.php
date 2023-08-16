<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core\Exceptions;

/**
 * Thrown when a configuration value is not present but should be
 */
class ConfigValueMissing extends \Exception
{
    public function __construct(string $missingKey)
    {
        parent::__construct('HubspotToSaleslink: Missing configuration value "' . $missingKey . '"');
    }
}
