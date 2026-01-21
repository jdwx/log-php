<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface;


/**
 * A simple way to register loggers for global discovery when a proper
 * service discovery implementation is not available.
 */
class LoggerRegistry {


    public const DEFAULT_LOGGER_ID = 'jdwx.logger.default';


    /** @var array<string, LoggerInterface> */
    public static array $rLoggers = [];


    public static function clear() : void {
        self::$rLoggers = [];
    }


    public static function get( string $i_stLoggerId = self::DEFAULT_LOGGER_ID ) : ?LoggerInterface {
        return self::$rLoggers[ $i_stLoggerId ] ?? null;
    }


    public static function register( LoggerInterface $i_logger, string $i_stLoggerId = self::DEFAULT_LOGGER_ID ) : void {
        self::$rLoggers[ $i_stLoggerId ] = $i_logger;
    }


    public static function unregister( string $i_stLoggerId = self::DEFAULT_LOGGER_ID ) : void {
        unset( self::$rLoggers[ $i_stLoggerId ] );
    }


}
