<?php


declare( strict_types = 1 );


namespace JDWX\Log;


abstract class AbstractDirectLogger implements HasLoggerInterface, LoggerInterface {


    use LoggerTrait;


    public function getLogger() : ?\Psr\Log\LoggerInterface {
        return $this;
    }


    public function hasLogger() : bool {
        return $this->getLogger() instanceof \Psr\Log\LoggerInterface;
    }


}
