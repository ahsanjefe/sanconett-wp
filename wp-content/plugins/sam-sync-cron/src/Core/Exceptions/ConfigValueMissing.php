<?php
/**
 * @package sam-to-localhost
 */

namespace Snapshot\SamSync\Core\Exceptions;

/**
 * Thrown when a configuration value is not present but should be
 */
class ConfigValueMissing extends \Exception
{
    public function __construct(string $missingKey)
    {
        parent::__construct('SamSync: Missing configuration value "' . $missingKey . '"');
    }
}
