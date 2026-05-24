<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use JsonSerializable;
use Stringable;


readonly class LogEntry implements Stringable, LogEntryInterface, JsonSerializable {


    use ReadOnlyValueTrait;


    public string $level;

    public string $message;

    /** @var mixed[] */
    public array $context;


    /** @param mixed[] $i_rContext */
    public function __construct( int|string             $i_level, string|Stringable $i_stMessage, array $i_rContext,
                                 private ?GlobalContext $gtx = null ) {
        $this->fromGlobalContext( $this->gtx );
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
        return LogTools::interpolate( $this->message, $this->context );
    }


    /** @return array<string, mixed> */
    public function jsonSerialize() : array {
        return [
            'level' => $this->level,
            'message' => $this->interpolatedMessage(),
            'context' => $this->value( $this->context ),
        ];
    }


    public function level() : string {
        return $this->level;
    }


    public function message() : string {
        return $this->message;
    }


    /** @param array<int|string, mixed> $i_rContext */
    public function withContext( array $i_rContext ) : self {
        return new self( $this->level, $this->message, $i_rContext, $this->gtx );
    }


}
