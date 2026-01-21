<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;


abstract class AbstractLogger implements LoggerInterface {


    use LoggerTrait;


    /**
     * The levels go UP as severity goes DOWN! I.e., DEBUG > WARNING > EMERGENCY
     * @deprecated Use LogLevels::compare(). Remove in 2.0.
     */
    public static function compareLevels( mixed $level1, mixed $level2 ) : int {
        return LogLevels::compare( $level1, $level2 );
    }


    /**
     * @deprecated Use LogLevels::toString(). Remove in 2.0.
     */
    public static function normalizeLevel( mixed $level, ?string $i_nstDefault = null ) : ?string {
        return LogLevels::toString( $level ) ?? $i_nstDefault;
    }


    /**
     * @deprecated Use LogLevels::toStringEx(). Remove in 2.0.
     */
    public static function normalizeLevelEx( mixed $level, ?string $i_nstDefault = null ) : string {
        $level = LogLevels::toString( $level ) ?? $i_nstDefault;
        if ( is_string( $level ) ) {
            return $level;
        }
        throw new InvalidArgumentException( 'Invalid log level' );
    }


    /**
     * @deprecated Use LogLevels::toInt(). Remove in 2.0.
     */
    public static function normalizeLevelInt( mixed $level, ?int $i_nstDefault = null ) : ?int {
        if ( is_int( $level ) ) {
            return $level;
        }
        if ( ! is_string( $level ) ) {
            throw new InvalidArgumentException( 'Invalid log level' );
        }
        return LogLevels::toInt( $level ) ?? $i_nstDefault;
    }


    /**
     * @deprecated Use LogLevels::toIntEx(). Remove in 2.0.
     */
    public static function normalizeLevelIntEx( mixed $level, ?int $i_niDefault = null ) : int {
        if ( is_int( $level ) ) {
            return $level;
        }
        if ( ! is_string( $level ) ) {
            throw new InvalidArgumentException( 'Invalid log level' );
        }
        $level = LogLevels::toInt( $level ) ?? $i_niDefault;
        if ( is_int( $level ) ) {
            return $level;
        }
        throw new InvalidArgumentException( 'Invalid log level' );
    }


}
