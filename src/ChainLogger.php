<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;


class ChainLogger implements LoggerInterface {


    use LoggerTrait;


    /** @var list<LoggerInterface> */
    private array $loggers = [];


    /** @param list<LoggerInterface>|LoggerInterface ...$i_loggers */
    public function __construct( array|LoggerInterface ...$i_loggers ) {
        foreach ( $i_loggers as $logger ) {
            $this->push( $logger );
        }
    }


    /**
     * @param int|string $level
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function log( mixed $level, \Stringable|string $message, array $context = [] ) : void {
        foreach ( $this->loggers as $logger ) {
            $logger->log( $level, $message, $context );
        }
    }


    /**
     * @param list<LoggerInterface>|LoggerInterface $logger
     * @return void
     */
    public function push( array|LoggerInterface $logger ) : void {
        if ( ! is_array( $logger ) ) {
            $this->loggers[] = $logger;
            return;
        }
        foreach ( $logger as $l ) {
            $this->push( $l );
        }
    }


}
