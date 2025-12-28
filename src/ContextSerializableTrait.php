<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use JsonSerializable;
use Stringable;


trait ContextSerializableTrait {


    /** @param array<int|string, mixed[]|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null>|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null $value */
    private static function serialize( array|bool|float|int|string|ContextSerializable|JsonSerializable|Stringable|null $value ) : mixed {
        if ( $value instanceof ContextSerializable ) {
            return $value->contextSerialize();
        }
        if ( $value instanceof JsonSerializable ) {
            return $value->jsonSerialize();
        }
        if ( $value instanceof Stringable ) {
            return strval( $value );
        }
        if ( is_array( $value ) ) {
            $r = [];
            foreach ( $value as $key => $subValue ) {
                $r[ strval( self::serialize( $key ) ) ] = self::serialize( $subValue );
            }
            return $r;
        }
        return $value;
    }


}