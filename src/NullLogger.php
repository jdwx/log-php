<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\AbstractLogger;


/**
 * Sometimes you just don't care.
 *
 * A logger that throws away everything logged.
 */
class NullLogger extends AbstractLogger {


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
