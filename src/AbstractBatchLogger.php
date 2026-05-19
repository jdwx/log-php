<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LogLevel;
use Stringable;


/**
 * This is a base class for log backends where it is more efficient to write a bunch
 * of entries at once.
 */
abstract class AbstractBatchLogger extends AbstractDirectLogger {


    /** @var list<LogEntry> */
    private array $rEntries = [];

    private string $level = LogLevel::DEBUG;

    /** @var array<int|string, mixed> */
    private array $rCommonContext = [];


    private bool $bUseCommonContext = true;


    public function __destruct() {
        if ( 0 === count( $this->rEntries ) ) {
            return;
        }
        $this->extractCommonContext();
        $this->rEntries[] = new LogEntry( LogLevel::DEBUG, 'implicit flush by destructor', [
            'class' => static::class,
        ] );
        $this->batch( $this->level, $this->rEntries, $this->rCommonContext );
    }


    public function flushLog() : void {
        if ( 0 !== count( $this->rEntries ) ) {
            $this->extractCommonContext();
            $this->batch( $this->level, $this->rEntries, $this->rCommonContext );
            $this->rEntries = [];
        }
        $this->rCommonContext = [];
        $this->level = LogLevel::DEBUG;
    }


    public function log( $level, string|Stringable $message, array $context = [] ) : void {
        $level = LogLevels::toStringEx( $level );
        $this->rEntries[] = new LogEntry(
            $level, LogTools::interpolate( $message, $context ), LogTools::value( $context )
        );
        $this->level = LogLevels::toStringEx( LogLevels::mostSevere( $this->level, $level ) );
    }


    public function setUseCommonContext( bool $i_bUseCommonContext ) : void {
        $this->bUseCommonContext = $i_bUseCommonContext;
    }


    /**
     * @param list<LogEntry>           $i_rLogMessages
     * @param array<int|string, mixed> $i_rCommonContext
     */
    abstract protected function batch( string $i_stLevel, array $i_rLogMessages, array $i_rCommonContext ) : void;


    protected function extractCommonContext() : void {
        if ( ! $this->bUseCommonContext ) {
            return;
        }
        $rCommonContext = [];
        $bFirst = true;
        foreach ( $this->rEntries as $entry ) {
            if ( $bFirst ) {
                $bFirst = false;
                $rCommonContext = $entry->context();
                continue;
            }
            $rContext = $entry->context();
            foreach ( $rCommonContext as $k => $v ) {
                if ( ! array_key_exists( $k, $rContext ) ) {
                    unset( $rCommonContext[ $k ] );
                    continue;
                }
                if ( $rContext[ $k ] !== $v ) {
                    unset( $rCommonContext[ $k ] );
                }
            }
        }

        $rNewEntries = [];
        foreach ( $this->rEntries as $entry ) {
            $rNewEntries[] = $entry->withContext( array_diff_key( $entry->context(), $rCommonContext ) );
        }

        $this->rEntries = $rNewEntries;
        $this->rCommonContext = $rCommonContext;

    }


}