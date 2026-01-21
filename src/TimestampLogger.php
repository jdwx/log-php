<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;


/**
 * Adds a timestamp to each log message. This is most useful in conjunction with
 * StderrLogger, which just dumps the log to stderr without decoration.
 */
class TimestampLogger extends AbstractLogger {


    public function __construct( private readonly LoggerInterface $parent, private readonly string $format = '[Y-m-d H:i:s] ' ) {}


    /**
     * @param int|string $level
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function log( $level, \Stringable|string $message, array $context = [] ) : void {
        $message = gmdate( $this->format ) . $message;
        $this->parent->log( $level, $message, $context );
    }


}
