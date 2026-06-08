<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use ArrayAccess;
use JsonSerializable;


/** @implements ArrayAccess<int|string, mixed> */
class GlobalContext implements ArrayAccess, JsonSerializable {


    use ValueTrait;


    /** @var array<int|string, mixed> */
    private array $rContext = [];


    /** @return array<int|string, mixed> */
    public function jsonSerialize() : array {
        return $this->value( $this->rContext, PHP_INT_MAX, null );
    }


    public function offsetExists( mixed $offset ) : bool {
        return isset( $this->rContext[ $offset ] );
    }


    public function offsetGet( mixed $offset ) : mixed {
        return $this->rContext[ $offset ] ?? null;
    }


    public function offsetSet( mixed $offset, mixed $value ) : void {
        assert( is_string( $offset ) || is_int( $offset ) );
        $this->rContext[ $offset ] = $value;
    }


    public function offsetUnset( mixed $offset ) : void {
        unset( $this->rContext[ $offset ] );
    }


    /** @return array<int|string, mixed> */
    public function valueContext( int  $i_uDepth = LogTools::DEFAULT_DEPTH,
                                  ?int $i_nuPropertyCount = LogTools::DEFAULT_PROPERTY_COUNT ) : array {
        return $this->value( $this->rContext, $i_uDepth, $i_nuPropertyCount );
    }


}
