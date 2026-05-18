<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LogLevel;
use Stringable;


/**
 * This is a base class for log backends where it is more efficient to write a bunch
 * of entries at once.
 */
abstract class AbstractBatchLogger extends AbstractLogger {


    /** @var list<LogEntry> */
    private array $rEntries = [];

    private string $level = LogLevel::DEBUG;


    public function __destruct() {
        if ( 0 === count( $this->rEntries ) ) {
            return;
        }
        $this->rEntries[] = new LogEntry( LogLevel::DEBUG, 'implicit flush by destructor', [
            'class' => static::class,
        ] );
        $this->batch( $this->level, $this->rEntries );
    }


    public function flushLog() : void {
        if ( 0 !== count( $this->rEntries ) ) {
            $this->batch( $this->level, $this->rEntries );
            $this->rEntries = [];
        }
        $this->level = LogLevel::DEBUG;
    }


    public function log( $level, string|Stringable $message, array $context = [] ) : void {
        $level = LogLevels::toStringEx( $level );
        $this->rEntries[] = new LogEntry(
            $level, FormattedLogger::interpolate( $message, $context ), FormattedLogger::value( $context )
        );
        $this->level = LogLevels::toStringEx( LogLevels::mostSevere( $this->level, $level ) );
    }


    /** @param list<LogEntry> $i_rLogMessages */
    abstract protected function batch( string $i_stLevel, array $i_rLogMessages ) : void;


}