<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Stringable;


abstract class FilterLogger extends AbstractDirectLogger {


    public function __construct( private readonly \Psr\Log\LoggerInterface $logger ) {}


    /**
     * @param string|int        $level
     * @param string|Stringable $message
     * @param mixed[]           $context
     * @return void
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function log( mixed $level, string|Stringable $message, array $context = [] ) : void {
        if ( ! $this->filter( $level, $message, $context ) ) {
            return;
        }
        $this->logger->log( $level, $message, $context );
    }


    /**
     * @param string|int        $level
     * @param string|Stringable $message
     * @param mixed[]           $context
     * @return bool True if the message should be logged, false otherwise.
     */
    abstract protected function filter( string|int $level, string|Stringable $message, array $context ) : bool;


}
