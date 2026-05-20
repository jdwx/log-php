<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Stringable;


abstract class AbstractProxyLogger extends AbstractLogger implements HasLoggerInterface, LoggerInterface {


    use LoggerExtraTrait;


    public function flushLog() : void {
        $logger = $this->getLogger();
        if ( $logger instanceof LoggerInterface ) {
            $logger->flushLog();
        }
    }


    public function hasLogger() : bool {
        $logger = $this->getLogger();
        if ( $logger instanceof HasLoggerInterface ) {
            return $logger->hasLogger();
        }
        return $logger instanceof PsrLoggerInterface;
    }


    public function log( $level, Stringable|string $message, array $context = [] ) : void {
        $this->getLogger()?->log( $level, $message, $context );
    }


}
