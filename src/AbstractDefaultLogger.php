<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface as PsrLoggerInterface;


/**
 * This is a helper class for implementing a default logger that
 * can be overridden as needed and initialized lazily.
 */
abstract class AbstractDefaultLogger extends ProxyLogger {


    private PsrLoggerInterface $defaultLogger;


    public function getLogger() : PsrLoggerInterface {
        return parent::getLogger() ?? $this->getDefaultLogger();
    }


    abstract protected function newDefaultLogger() : PsrLoggerInterface;


    private function getDefaultLogger() : PsrLoggerInterface {
        if ( ! isset( $this->defaultLogger ) ) {
            $this->defaultLogger = $this->newDefaultLogger();
        }
        return $this->defaultLogger;
    }


}
