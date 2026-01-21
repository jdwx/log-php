<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;


final class LogLevels {


    /**
     * Compares two log levels.
     *
     * Log levels are compared based on their severity, with lower numeric values
     * indicating higher severity. (E.g., LOG_EMERG at 0 is more severe than LOG_DEBUG at 7.)
     *
     * @return int Returns positive if $i_level1 is less severe than $i_level2,
     *             0 if they are equal, and negative if $i_level1 is more
     *             severe than $i_level2.
     */
    public static function compare( int|string $i_level1, int|string $i_level2 ) : int {
        $iLevel1 = self::toIntEx( $i_level1 );
        $iLevel2 = self::toIntEx( $i_level2 );
        return $iLevel1 <=> $iLevel2;
    }


    public static function isValid( int|string $i_level ) : bool {
        if ( is_int( $i_level ) ) {
            return in_array( $i_level, [
                LOG_EMERG,
                LOG_ALERT,
                LOG_CRIT,
                LOG_ERR,
                LOG_WARNING,
                LOG_NOTICE,
                LOG_INFO,
                LOG_DEBUG,
            ], true );
        }
        $i_level = strtolower( trim( $i_level ) );
        return in_array( $i_level, [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ], true );
    }


    public static function leastSevere( int|string ...$levels ) : int|string {
        if ( empty( $levels ) ) {
            throw new \InvalidArgumentException( 'At least one log level must be provided' );
        }
        $max = array_shift( $levels );
        while ( ! empty( $levels ) ) {
            $next = array_shift( $levels );
            if ( self::compare( $next, $max ) > 0 ) {
                $max = $next;
            }
        }
        return $max;
    }


    public static function mostSevere( int|string ...$levels ) : int|string {
        if ( empty( $levels ) ) {
            throw new \InvalidArgumentException( 'At least one log level must be provided' );
        }
        $min = array_shift( $levels );
        while ( ! empty( $levels ) ) {
            $next = array_shift( $levels );
            if ( self::compare( $next, $min ) < 0 ) {
                $min = $next;
            }
        }
        return $min;
    }


    public static function toInt( int|string $i_level ) : ?int {
        if ( is_string( $i_level ) ) {
            $i_level = strtolower( trim( $i_level ) );
        }
        return match ( $i_level ) {
            LogLevel::EMERGENCY, LOG_EMERG => LOG_EMERG,
            LogLevel::ALERT, LOG_ALERT => LOG_ALERT,
            LogLevel::CRITICAL, LOG_CRIT => LOG_CRIT,
            LogLevel::ERROR, LOG_ERR => LOG_ERR,
            LogLevel::WARNING, LOG_WARNING => LOG_WARNING,
            LogLevel::NOTICE, LOG_NOTICE => LOG_NOTICE,
            LogLevel::INFO, LOG_INFO => LOG_INFO,
            LogLevel::DEBUG, LOG_DEBUG => LOG_DEBUG,
            default => null,
        };
    }


    public static function toIntEx( int|string $i_level ) : int {
        $iLevel = self::toInt( $i_level );
        if ( is_int( $iLevel ) ) {
            return $iLevel;
        }
        throw new InvalidArgumentException( "Invalid log level: {$i_level}" );
    }


    public static function toString( int|string $i_level ) : ?string {
        if ( is_string( $i_level ) ) {
            $i_level = strtolower( trim( $i_level ) );
        }
        return match ( $i_level ) {
            LogLevel::EMERGENCY, LOG_EMERG => LogLevel::EMERGENCY,
            LogLevel::ALERT, LOG_ALERT => LogLevel::ALERT,
            LogLevel::CRITICAL, LOG_CRIT => LogLevel::CRITICAL,
            LogLevel::ERROR, LOG_ERR => LogLevel::ERROR,
            LogLevel::WARNING, LOG_WARNING => LogLevel::WARNING,
            LogLevel::NOTICE, LOG_NOTICE => LogLevel::NOTICE,
            LogLevel::INFO, LOG_INFO => LogLevel::INFO,
            LogLevel::DEBUG, LOG_DEBUG => LogLevel::DEBUG,
            default => null,
        };
    }


    public static function toStringEx( int|string $i_level ) : string {
        $stLevel = self::toString( $i_level );
        if ( is_string( $stLevel ) ) {
            return $stLevel;
        }
        throw new InvalidArgumentException( "Invalid log level: {$i_level}" );
    }


}
