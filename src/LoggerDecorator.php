<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Stringable;


class LoggerDecorator implements LoggerInterface {


    use LoggerTrait;


    public function __construct( private readonly \Psr\Log\LoggerInterface $logger ) {
    }


    public function log( $level, string|Stringable $message, array $context = [] ) : void {
        $this->logger->log( $level, $message, $context );
    }


}
