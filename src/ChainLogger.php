<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Stringable;


class ChainLogger extends AbstractDirectLogger {


    /** @var list<PsrLoggerInterface> */
    private array $loggers = [];


    /** @param list<PsrLoggerInterface>|PsrLoggerInterface ...$i_loggers */
    public function __construct( array|PsrLoggerInterface ...$i_loggers ) {
        foreach ( $i_loggers as $logger ) {
            $this->push( $logger );
        }
    }


    public function flushLog() : void {
        foreach ( $this->loggers as $logger ) {
            if ( $logger instanceof LoggerInterface ) {
                $logger->flushLog();
            }
        }
    }


    /**
     * @return PsrLoggerInterface|null
     *
     * Because this is considered a direct logger, it returns itself if it
     * finds any suitable loggers.
     */
    public function getLogger() : ?PsrLoggerInterface {
        foreach ( $this->loggers as $logger ) {
            if ( ! $logger instanceof HasLoggerInterface ) {
                return $this;
            }
            $x = $logger->getLogger();
            if ( $x instanceof PsrLoggerInterface ) {
                return $this;
            }
        }
        return null;
    }


    /**
     * @param int|string $level
     * @suppress PhanTypeMismatchDeclaredParamNullable
     *
     * Does not check level; relies on underlying loggers to do that.
     */
    public function log( mixed $level, string|Stringable $message, array $context = [] ) : void {
        foreach ( $this->loggers as $logger ) {
            $logger->log( $level, $message, $context );
        }
    }


    /**
     * @param list<PsrLoggerInterface>|PsrLoggerInterface $logger
     * @return void
     */
    public function push( array|PsrLoggerInterface $logger ) : void {
        if ( ! is_array( $logger ) ) {
            $this->loggers[] = $logger;
            return;
        }
        foreach ( $logger as $l ) {
            $this->push( $l );
        }
    }


}
