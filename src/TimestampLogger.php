<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;


/**
 * Adds a timestamp to each log message.
 */
class TimestampLogger extends AbstractLogger {


    public function __construct( private readonly LoggerInterface $parent ) {}


    /**
     * @param int|string $level
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function log( $level, \Stringable|string $message, array $context = [] ) : void {
        $message = gmdate( '[Y-m-d H:i:s] ' ) . $message;
        $this->parent->log( $level, $message, $context );
    }


}
