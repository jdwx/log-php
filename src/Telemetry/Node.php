<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


use JDWX\Log\ContextSerializable;
use JDWX\Log\ContextSerializableTrait;
use JDWX\Log\TimestampedLogEntry;
use JsonSerializable;
use Psr\Log\LoggerTrait;
use Stringable;


class Node implements NodeInterface {


    use LoggerTrait;

    use ContextSerializableTrait;


    private bool $bFinished = false;


    /** @var array<string, mixed[]|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null> */
    private array $rContext = [];


    /** @var list<mixed[]|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null> */
    private array $rChildren = [];


    private float $startTime;


    public function __construct() {
        $this->startTime = microtime( true );
    }


    /** @param array<string, mixed[]|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null> $i_rContext */
    public function addContext( array $i_rContext ) : void {
        foreach ( $i_rContext as $stKey => $value ) {
            $this->setContext( $stKey, $value );
        }
    }


    /** @return array<int|string, mixed[]|bool|float|int|string|null> */
    public function contextSerialize() : array {
        $r = self::serialize( $this->getContext() );
        foreach ( $this->rChildren as $child ) {
            $r[] = self::serialize( $child );
        }
        return $r;
    }


    public function finish() : void {
        $stopTime = microtime( true );
        $duration = $stopTime - $this->startTime;
        $this->setContext( 'startTime', $this->startTime );
        $this->setContext( 'endTime', microtime( true ) );
        $this->setContext( 'duration', $duration );
        $this->bFinished = true;
    }


    /** @return array<string, mixed[]|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null> */
    public function getContext() : array {
        return $this->rContext;
    }


    public function log( $level, Stringable|string $message, array $context = [] ) : void {
        $this->push( new TimestampedLogEntry( $level, $message, $context ) );
    }


    /** @param mixed[]|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null $i_value */
    public function push( array|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null $i_value ) : void {
        if ( $this->bFinished ) {
            throw new \LogicException( 'Telemetry node child pushed after finish.' );
        }
        $this->rChildren[] = $i_value;
    }


    /** @param mixed[]|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null $i_value */
    public function setContext( string                                                                           $i_stKey,
                                array|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null $i_value ) : void {
        if ( $this->bFinished ) {
            throw new \LogicException( 'Telemetry node context set after finish.' );
        }
        $this->rContext[ $i_stKey ] = $i_value;
    }


}
