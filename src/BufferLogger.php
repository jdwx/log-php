<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Countable;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Stringable;


class BufferLogger implements LoggerInterface, Countable {


    use LoggerTrait;


    /** @var LogEntry[] */
    private array $rLogs = [];


    public function count() : int {
        return count( $this->rLogs );
    }


    public function empty() : bool {
        return empty( $this->rLogs );
    }


    public function log( mixed $level, string|Stringable $message, array $context = [] ) : void {
        $this->rLogs[] = new LogEntry( $level, $message, $context );
    }


    public function shiftLog() : ?LogEntry {
        return array_shift( $this->rLogs );
    }


    public function shiftLogEx() : LogEntry {
        $log = $this->shiftLog();
        if ( $log instanceof LogEntry ) {
            return $log;
        }
        throw new \RuntimeException( 'No log entry available' );
    }


}
