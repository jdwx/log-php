<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Stringable;


readonly class LogEntry implements Stringable {


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
        return "[{$this->level}] {$this->message}";
    }


}
