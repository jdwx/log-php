<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Stringable;


/**
 * This class is used to make a PSR LoggerInterface
 * compatible with the LoggerInterface in this library.
 */
class LoggerDecorator implements LoggerInterface {


    use LoggerTrait;


    public function __construct( private readonly \Psr\Log\LoggerInterface $logger ) {
    }


    public function getLogger() : \Psr\Log\LoggerInterface {
        return $this->logger;
    }


    public function log( $level, string|Stringable $message, array $context = [] ) : void {
        $this->logger->log( $level, $message, $context );
    }


}
