<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;


/**
 * Sometimes you just don't care.
 *
 * A logger that throws away everything logged.
 */
class NullLogger extends AbstractLogger {


    public function getLogger() : ?LoggerInterface {
        return null;
    }


    /**
     * @param int|string $level
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function log( mixed $level, \Stringable|string $message, array $context = [] ) : void {
        LogLevels::toIntEx( $level );
    }


}
