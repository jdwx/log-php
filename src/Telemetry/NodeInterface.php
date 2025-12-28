<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


use JDWX\Log\ContextSerializable;
use Psr\Log\LoggerInterface;


interface NodeInterface extends LoggerInterface, ContextSerializable {


    /** @param array<string, mixed[]|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null> $i_rContext */
    public function addContext( array $i_rContext ) : void;


    /** @return array<string, mixed[]|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null> */
    public function getContext() : array;


    /** @param mixed[]|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null $i_value */
    public function setContext( string                                                                             $i_stKey,
                                array|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null $i_value ) : void;


}