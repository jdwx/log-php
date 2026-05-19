<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\AbstractLogger;
use Stringable;


/**
 * Sometimes you just don't care.
 *
 * A logger that throws away everything logged.
 */
class NullLogger extends AbstractLogger {


    use LoggerExtraTrait;


    public function getLogger() : ?LoggerInterface {
        return null;
    }


    public function hasLogger() : bool {
        return false;
    }


    /**
     * @param int|string               $level
     * @param Stringable|string        $message
     * @param array<int|string, mixed> $context
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function log( mixed $level, Stringable|string $message, array $context = [] ) : void {
        LogLevels::toIntEx( $level );
    }


}
