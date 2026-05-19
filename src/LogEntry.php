<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Stringable;


readonly class LogEntry implements Stringable, LogEntryInterface {


    public string $level;

    public string $message;

    /** @var mixed[] */
    public array $context;


    /** @param mixed[] $i_rContext */
    public function __construct( int|string $i_level, string|Stringable $i_stMessage, array $i_rContext ) {
        $this->level = LogLevels::toString( $i_level ) ?? "INVALID({$i_level})";
        $this->message = strval( $i_stMessage );
        $this->context = $i_rContext;
    }


    public function __toString() : string {
        return "[{$this->level}] " . $this->interpolatedMessage();
    }


    /** @return mixed[] */
    public function context() : array {
        return $this->context;
    }


    public function interpolatedMessage() : string {
        return FormattedLogger::interpolate( $this->message, $this->context );
    }


    public function level() : string {
        return $this->level;
    }


    public function message() : string {
        return $this->message;
    }


    /** @param array<int|string, mixed> $i_rContext */
    public function withContext( array $i_rContext ) : self {
        return new self( $this->level, $this->message, $i_rContext );
    }


}
