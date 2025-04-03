<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;


abstract class AbstractLogger implements LoggerInterface {


    use LoggerTrait;


    public static function normalizeLevel( mixed $level, ?string $i_nstDefault = null ) : ?string {
        if ( is_string( $level ) ) {
            $level = strtolower( trim( $level ) );
        }
        return match ( $level ) {
            LOG_DEBUG, LogLevel::DEBUG => LogLevel::DEBUG,
            LOG_INFO, LogLevel::INFO => LogLevel::INFO,
            LOG_NOTICE, LogLevel::NOTICE => LogLevel::NOTICE,
            LOG_WARNING, LogLevel::WARNING => LogLevel::WARNING,
            LOG_ERR, LogLevel::ERROR => LogLevel::ERROR,
            LOG_CRIT, LogLevel::CRITICAL => LogLevel::CRITICAL,
            LOG_ALERT, LogLevel::ALERT => LogLevel::ALERT,
            LOG_EMERG, LogLevel::EMERGENCY => LogLevel::EMERGENCY,
            default => $i_nstDefault,
        };
    }


    public static function normalizeLevelEx( mixed $level, ?string $i_nstDefault = null ) : string {
        $level = self::normalizeLevel( $level, $i_nstDefault );
        if ( is_string( $level ) ) {
            return $level;
        }
        throw new \InvalidArgumentException( 'Invalid log level' );
    }


}
