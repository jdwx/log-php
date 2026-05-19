<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface as PsrLoggerInterface;


/**
 * This is a direct proxy to another LoggerInstance with no additional functionality.
 *
 * This class is useful as a base class for log decorators or in situations where
 * you need to initialize a LoggerInterface consumer before the mechanism you want
 * it to use for logging is fully initialized.
 */
class ProxyLogger extends AbstractProxyLogger {


    public function __construct( private ?PsrLoggerInterface $logger = null ) {}


    public function getLogger() : ?PsrLoggerInterface {
        return $this->logger;
    }


    public function setLogger( ?PsrLoggerInterface $i_logger = null ) : void {
        $this->logger = $i_logger;
    }


}
