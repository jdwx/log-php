<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Stringable;


/**
 * This is a direct proxy with no additional functionality.
 *
 * This class is intended to be used in situations where you need to initialize
 * a LoggerInterface consumer before the mechanism you want it to use for logging
 * is fully initialized.
 *
 * For example, this can help provide "last ditch" logging during program startup
 * before the main logging mechanism is initialized without sticking early
 * logging consumers with the last-ditch mechanism for the whole runtime.
 */
class LoggerContainer implements LoggerInterface {


    use LoggerTrait;


    private ?LoggerInterface $logger;


    public function __construct( ?\Psr\Log\LoggerInterface $logger = null ) {
        $this->setLogger( $logger );
    }


    public function getLogger() : ?LoggerInterface {
        return $this->logger;
    }


    public function hasLogger() : bool {
        return $this->logger instanceof LoggerInterface;
    }


    public function log( $level, Stringable|string $message, array $context = [] ) : void {
        $this->logger?->log( $level, $message, $context );
    }


    public function setLogger( ?\Psr\Log\LoggerInterface $logger = null ) : void {
        if ( ! is_null( $logger ) && ! $logger instanceof LoggerInterface ) {
            $logger = new LoggerDecorator( $logger );
        }
        $this->logger = $logger;
    }


}
