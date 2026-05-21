<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use JsonSerializable;
use Stringable;
use Throwable;


class LogTools {


    public static function escape( mixed $i_x ) : mixed {
        return match ( true ) {
            is_string( $i_x ) => str_replace( [ "\t", "\n", "\r", chr( 0 ) ], [ '\\t', '\\n', '\\r', '\\0' ], $i_x ),
            is_array( $i_x ) => self::escapeArray( $i_x ),
            default => $i_x,
        };
    }


    /**
     * @param array<int|string, mixed> $i_r
     * @return array<int|string, mixed>
     */
    public static function escapeArray( array $i_r ) : array {
        $rOut = [];
        foreach ( $i_r as $k => $v ) {
            $rOut[ self::escape( $k ) ] = self::escape( $v );
        }
        return $rOut;
    }


    /** @return array<string, mixed> */
    public static function exceptionToArray( Throwable $x, int $i_uDepth = 3, int $i_uPropertyCount = 6, ?VisitedCheck $i_visited = null ) : array {
        $i_visited ??= new VisitedCheck();
        $r = [
            'class' => $x::class,
            'message' => $x->getMessage(),
            'code' => $x->getCode(),
            'file' => $x->getFile(),
            'line' => $x->getLine(),
            'trace' => $x->getTraceAsString(),
        ];
        $prev = $x->getPrevious();
        if ( $prev instanceof Throwable ) {
            if ( $i_uDepth <= 1 || ! $i_visited->visit( $prev ) ) {
                $r[ 'previous' ] = $prev::class . '#' . spl_object_id( $prev );
            } else {
                $r[ 'previous' ] = static::exceptionToArray( $prev, $i_uDepth - 1, $i_uPropertyCount, $i_visited );
            }
        }
        return $r;
    }


    public static function format( mixed $i_x, int $i_uDepth = 3, ?int $i_nuPropertyCount = 5 ) : string {
        if ( is_string( $i_x ) || $i_x instanceof Stringable ) {
            return '"' . str_replace( '"', '\\"', strval( $i_x ) ) . '"';
        }
        if ( is_object( $i_x ) ) {
            return self::formatObject( $i_x, $i_uDepth, $i_nuPropertyCount );
        }
        if ( is_array( $i_x ) ) {
            return self::formatArray( $i_x, $i_uDepth, $i_nuPropertyCount );
        }
        if ( is_null( $i_x ) ) {
            return 'null';
        }
        if ( is_bool( $i_x ) ) {
            return $i_x ? 'true' : 'false';
        }
        return strval( self::value( $i_x, $i_uDepth, $i_nuPropertyCount ) );
    }


    /** @param array<string, mixed> $i_r */
    public static function formatArray( array|object $i_r, int $i_uDepth = 3, ?int $i_nuPropertyCount = 5 ) : string {
        return self::formatArrayInner( self::value( $i_r, $i_uDepth, $i_nuPropertyCount ), 0 );
    }


    public static function formatObject( object $i_obj, int $i_uDepth = 3, ?int $i_nuPropertyCount = 5 ) : string {
        return $i_obj::class . '#' . spl_object_id( $i_obj ) . ' '
            . self::formatArrayInner( self::escape( self::objectProperties( $i_obj, $i_uDepth, $i_nuPropertyCount ) ), 0 );
    }


    /**
     * @param array<int|string, mixed> $i_rContext
     */
    public static function interpolate( string|Stringable $i_message, array $i_rContext ) : string {
        $replace = [];
        foreach ( $i_rContext as $key => $val ) {
            # check that the key doesn't contain any invalid characters
            $key = strval( $key ); # stupid integer key quirk
            if ( ! preg_match( '/^[a-zA-Z0-9_.]+$/', $key ) ) {
                continue;
            }

            # check that the value can be cast to string
            if ( ! is_array( $val ) && ( ! is_object( $val ) || method_exists( $val, '__toString' ) ) ) {
                $replace[ '{' . $key . '}' ] = $val;
            }
        }

        return strtr( strval( $i_message ), $replace );
    }


    /**
     * @param array<int|string, mixed> $i_rValues     The values to limit.
     * @param int                      $i_uDepth      The number of levels to recurse into nested arrays and objects.
     * @param int|null                 $i_nuMaxValues The maximum number of values to include in the output.
     * @param ?VisitedCheck            $i_visited     List of classes that have already been seen.
     * @return array<int|string, mixed> The input array with limited values.
     *
     * Used to limit arrays to a specified number of (loggable) values, considering both depth and number of elements.
     * This is used to prevent spewing pages and pages of large objects into logs.
     */
    public static function limitArray( array         $i_rValues, int $i_uDepth = 3, ?int $i_nuMaxValues = 5,
                                       ?VisitedCheck $i_visited = null ) : array {
        if ( $i_uDepth < 1 ) {
            $i_nuMaxValues = 0;
        }

        $bTooMany = is_int( $i_nuMaxValues ) && count( $i_rValues ) > $i_nuMaxValues;
        if ( $bTooMany ) {
            $i_rValues = array_slice( $i_rValues, 0, $i_nuMaxValues );
        }
        $rOut = [];
        foreach ( $i_rValues as $k => $v ) {
            $v = self::valueInner( $v, $i_uDepth - 1, $i_nuMaxValues, $i_visited );
            $rOut[ $k ] = $v;
        }
        if ( $bTooMany ) {
            $rOut[] = '...';
        }
        return $rOut;
    }


    /**
     * @param object            $i_obj
     * @param int               $i_uDepth
     * @param int|null          $i_nuPropertyCount
     * @param VisitedCheck|null $i_visited
     * @return array<int|string, mixed> Object as array with class name and id as elements of the array.
     */
    public static function objectAsArray( object $i_obj, int $i_uDepth = 3, ?int $i_nuPropertyCount = 5, ?VisitedCheck $i_visited = null ) : array {
        $r = [
            'object$class' => $i_obj::class,
            'object$id' => spl_object_id( $i_obj ),
        ];
        if ( $i_uDepth < 1 ) {
            return $r;
        }
        return array_merge( $r, self::objectProperties( $i_obj, $i_uDepth, $i_nuPropertyCount, $i_visited ) );
    }


    /**
     * @param object        $i_obj             The object to extract properties from.
     * @param int           $i_uDepth          The depth of recursion for nested objects.
     * @param int|null      $i_nuPropertyCount The maximum number of properties to include.
     * @param ?VisitedCheck $i_visited         List of classes that have already been seen.
     * @return array<int|string, mixed>
     *
     * Gets (loggable) properties of an object, optionally limiting depth and number of properties.
     */
    public static function objectProperties( object $i_obj, int $i_uDepth = 3, ?int $i_nuPropertyCount = 5, ?VisitedCheck $i_visited = null ) : array {
        $rProperties = method_exists( $i_obj, '__debugInfo' )
            ? $i_obj->__debugInfo()
            : get_object_vars( $i_obj );
        return self::limitArray( $rProperties, $i_uDepth, $i_nuPropertyCount, $i_visited );
    }


    /**
     * @param mixed $i_xValue The input value.
     * @return mixed The loggable value.
     *
     * Prepare an arbitrary value to be represented in a log context without
     * converting it to a string (if possible). I.e., after running this, the returned value
     * will be a scalar type, a string, or an array (possible nested) of scalar
     * types or strings. Objects that don't have a specific representation will
     * be returned as arrays of their properties with additional keys
     * object$class and object$id.
     */
    public static function value( mixed $i_xValue, int $i_uDepth = 3, ?int $i_nuPropertyCount = 5 ) : mixed {
        return self::escape( self::valueInner( $i_xValue, $i_uDepth, $i_nuPropertyCount ) );
    }


    /**
     * @param object        $i_obj
     * @param int           $i_uDepth
     * @param int|null      $i_nuPropertyCount
     * @param ?VisitedCheck $i_visited List of classes that have already been seen.
     * @return array<int|string, mixed>|string
     */
    public static function valueObject( object $i_obj, int $i_uDepth = 3, ?int $i_nuPropertyCount = 5, ?VisitedCheck $i_visited = null ) : array|string {
        $i_visited ??= new VisitedCheck();
        if ( ! $i_visited->visit( $i_obj ) ) {
            $i_uDepth = 0;
        }
        $r = self::objectAsArray( $i_obj, $i_uDepth, $i_nuPropertyCount, $i_visited );
        if ( 2 === count( $r ) ) {
            return implode( '#', $r );
        }
        return $r;
    }


    /**
     * @param array<string, mixed> $i_r
     * @param int                  $i_uIndent The number of spaces to indent nested arrays.
     * @return string The formatted string representation of the array.
     */
    private static function formatArrayInner( array $i_r, int $i_uIndent ) : string {
        $stIndent = str_repeat( ' ', $i_uIndent );
        $st = "{\n";
        foreach ( $i_r as $stKey => $xValue ) {
            $st .= "{$stIndent}  {$stKey}: ";
            if ( is_array( $xValue ) ) {
                $st .= 'array ' . self::formatArrayInner( $xValue, $i_uIndent + 2 );
            } elseif ( is_bool( $xValue ) ) {
                $st .= $xValue ? 'true' : 'false';
                $st .= "\n";
            } elseif ( is_null( $xValue ) ) {
                $st .= 'null';
                $st .= "\n";
            } elseif ( is_string( $xValue ) ) {
                $st .= '"' . str_replace( '"', '\\"', $xValue ) . '"';
                $st .= "\n";
            } else {
                $st .= $xValue;
                $st .= "\n";
            }
        }
        $st .= "{$stIndent}}\n";
        return $st;
    }


    private static function valueContext( ContextSerializable $i_ctx, int $i_uDepth, ?int $i_nuPropertyCount, VisitedCheck $i_visited ) : mixed {
        if ( ! $i_visited->visit( $i_ctx ) ) {
            return $i_ctx::class . '#' . spl_object_id( $i_ctx );
        }
        return self::valueInner( $i_ctx->contextSerialize(), $i_uDepth, $i_nuPropertyCount, $i_visited );
    }


    private static function valueInner( mixed $i_xValue, int $i_uDepth = 3, ?int $i_nuPropertyCount = 5, ?VisitedCheck $i_visited = null ) : mixed {
        $i_visited ??= new VisitedCheck();
        return match ( true ) {
            is_bool( $i_xValue ), is_float( $i_xValue ), is_int( $i_xValue ),
            is_null( $i_xValue ), is_string( $i_xValue ) => $i_xValue,
            is_array( $i_xValue ) => self::limitArray( $i_xValue, $i_uDepth, $i_nuPropertyCount, $i_visited ),
            is_resource( $i_xValue ) => get_resource_type( $i_xValue ) . '(' . get_resource_id( $i_xValue ) . ')',
            // @codeCoverageIgnoreStart
            ! is_object( $i_xValue ) => gettype( $i_xValue ) . '(' . $i_xValue . ')',
            // @codeCoverageIgnoreEnd
            $i_xValue instanceof ContextSerializable => self::valueContext( $i_xValue, $i_uDepth, $i_nuPropertyCount, $i_visited ),
            $i_xValue instanceof JsonSerializable => self::valueJson( $i_xValue, $i_uDepth, $i_nuPropertyCount, $i_visited ),
            $i_xValue instanceof Throwable => self::exceptionToArray( $i_xValue, $i_uDepth, max( 6, $i_nuPropertyCount ) ),
            $i_xValue instanceof \BackedEnum => $i_xValue::class . '( ' . $i_xValue->name . ': ' . $i_xValue->value . ')',
            $i_xValue instanceof \UnitEnum => $i_xValue::class . '( ' . $i_xValue->name . ')',
            $i_xValue instanceof Stringable => $i_xValue::class . '(' . $i_xValue->__toString() . ')',
            default => self::valueObject( $i_xValue, $i_uDepth, $i_nuPropertyCount, $i_visited ),
        };
    }


    private static function valueJson( JsonSerializable $i_jso, int $i_uDepth, ?int $i_nuPropertyCount, VisitedCheck $i_visited ) : mixed {
        if ( ! $i_visited->visit( $i_jso ) ) {
            return $i_jso::class . '#' . spl_object_id( $i_jso );
        }
        return self::valueInner( $i_jso->jsonSerialize(), $i_uDepth, $i_nuPropertyCount, $i_visited );
    }


}
