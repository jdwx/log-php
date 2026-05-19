<?php


declare( strict_types = 1 );


namespace JDWX\Log;


/**
 * This is a helper class for implementing a default logger that
 * can be overridden as needed and initialized lazily.
 */
abstract class AbstractDefaultLogger extends LoggerContainer {


    private LoggerInterface $defaultLogger;


    public function getLogger() : LoggerInterface {
        return parent::getLogger() ?? $this->getDefaultLogger();
    }


    abstract protected function newDefaultLogger() : LoggerInterface;


    private function getDefaultLogger() : LoggerInterface {
        if ( ! isset( $this->defaultLogger ) ) {
            $this->defaultLogger = $this->newDefaultLogger();
        }
        return $this->defaultLogger;
    }


}
