<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core\Logging;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Processor\UidProcessor;
use Snapshot\HubSpotToSalesLink\Core\Config;

/**
 * Helper class to generate a new Monolog instance
 */
class LoggerFactory
{
    /**
     * Creates an instance of Monolog\Logger.
     *
     * - Includes a unique ID on each log record.
     * - Logs to a daily rotating file
     * - Formatted as JSON
     *
     * @return Logger
     */
    public static function make(string $namespace): Logger
    {
        $logger = new Logger('hubspot-to-saleslink');

        // Add a unique ID to each record. The intention is that all
        // logs for a single request share the same ID.
        $logger->pushProcessor(new UidProcessor(16));

        // Use a daily rotating log
        $handler = new RotatingFileHandler(Config::getLogPath() . '/' . $namespace . '/log');
        // Use JSON log format for easy parsing later
        $formatter = new JsonFormatter();
        // $formatter->setJsonPrettyPrint(true); // can be enabled for easier debugging
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        return $logger;
    }
}
