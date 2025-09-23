<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;


abstract class AbstractLogger implements LoggerInterface {


    use LoggerTrait;


    /**
     * The levels go UP as severity goes DOWN! I.e., DEBUG > WARNING > EMERGENCY
     */
    public static function compareLevels( mixed $level1, mixed $level2 ) : int {
        return self::normalizeLevelIntEx( $level1, null ) <=> self::normalizeLevelIntEx( $level2, null );
    }


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


    public static function normalizeLevelInt( mixed $level, ?int $i_nstDefault = null ) : ?int {
        if ( is_int( $level ) ) {
            return $level;
        }
        if ( is_string( $level ) ) {
            $level = strtolower( trim( $level ) );
        }
        return match ( $level ) {
            LOG_DEBUG, LogLevel::DEBUG => LOG_DEBUG,
            LOG_INFO, LogLevel::INFO => LOG_INFO,
            LOG_NOTICE, LogLevel::NOTICE => LOG_NOTICE,
            LOG_WARNING, LogLevel::WARNING => LOG_WARNING,
            LOG_ERR, LogLevel::ERROR => LOG_ERR,
            LOG_CRIT, LogLevel::CRITICAL => LOG_CRIT,
            LOG_ALERT, LogLevel::ALERT => LOG_ALERT,
            LOG_EMERG, LogLevel::EMERGENCY => LOG_EMERG,
            default => $i_nstDefault,
        };
    }


    public static function normalizeLevelIntEx( mixed $level, ?int $i_nstDefault = null ) : int {
        $level = self::normalizeLevelInt( $level, $i_nstDefault );
        if ( is_int( $level ) ) {
            return $level;
        }
        throw new \InvalidArgumentException( 'Invalid log level' );
    }


}
