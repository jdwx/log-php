<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LogLevel;


class DebugFilterLogger extends FilterLogger {


    /**
     * @inheritDoc
     */
    protected function filter( mixed $level, \Stringable|string $message, array $context ) : bool {
        return static::normalizeLevel( $level ) !== LogLevel::DEBUG;
    }


}
