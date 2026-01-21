<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


use JDWX\Log\ContextSerializable;
use JDWX\Log\LevelInterface;
use Psr\Log\LoggerInterface;


interface NodeInterface extends ContextSerializable, LevelInterface, LoggerInterface {


    /** @param array<string, mixed[]|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null> $i_rContext */
    public function addContext( array $i_rContext ) : void;


    public function finish() : void;


    /** @return array<string, mixed[]|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null> */
    public function getContext() : array;


    public function level() : string;


    /** @param mixed[]|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null $i_value */
    public function setContext( string                                                                             $i_stKey,
                                array|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null $i_value ) : void;


}