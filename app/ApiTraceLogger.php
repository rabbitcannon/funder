<?php
namespace App;
use CSUNMetaLab\MultipleLogs\Loggers\Logger;

class ApiTraceLogger extends Logger
{
    /**
     * Constructs a new PurchaseLogger object.
     *
     * @param string $path The path to the log file
     * @param string $logLevel Optional parameter to specify minimum log level
     */
    public function __construct( $logLevel="debug") {
        parent::__construct( storage_path('logs/api_trace.log'), 'api_trace', $logLevel);
    }
}