<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use JsonSerializable;
use Stringable;
use Throwable;


class LogTools {


    /** @return array<string, mixed> */
    public static function exceptionToArray( Throwable $x ) : array {
        $r = [
            'class' => get_class( $x ),
            'message' => $x->getMessage(),
            'code' => $x->getCode(),
            'file' => $x->getFile(),
            'line' => $x->getLine(),
            'trace' => $x->getTraceAsString(),
        ];
        $prev = $x->getPrevious();
        if ( $prev instanceof Throwable ) {
            $r[ 'previous' ] = static::exceptionToArray( $prev );
        }
        return $r;
    }


    /** @param array<string, mixed> $i_r */
    public static function formatArray( array $i_r ) : string {
        return self::formatArrayInner( $i_r, 0 );
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
     * @param mixed $i_xValue The input value.
     * @return mixed The loggable value.
     *
     * Prepare an arbitrary value to be represented in a log context without
     * converting it to a string. I.e., after running this, the returned value
     * will be a scalar type, a string, or an array (possible nested) of scalar
     * types or strings.
     */
    public static function value( mixed $i_xValue, ?int $i_nuPropertyCount = 5 ) : mixed {
        $x = match ( true ) {
            is_bool( $i_xValue ), is_float( $i_xValue ), is_int( $i_xValue ),
            is_null( $i_xValue ), is_string( $i_xValue ) => $i_xValue,
            is_array( $i_xValue ) =>
            is_int( $i_nuPropertyCount )
                ? array_slice( array_map( fn( $x ) => self::value( $x ), $i_xValue ), 0, $i_nuPropertyCount )
                : array_map( fn( $x ) => self::value( $x ), $i_xValue ),
            is_resource( $i_xValue ) => get_resource_type( $i_xValue ) . '(' . get_resource_id( $i_xValue ) . ')',
            // @codeCoverageIgnoreStart
            ! is_object( $i_xValue ) => gettype( $i_xValue ) . '(' . $i_xValue . ')',
            // @codeCoverageIgnoreEnd
            $i_xValue instanceof ContextSerializable => self::value( $i_xValue->contextSerialize() ),
            $i_xValue instanceof JsonSerializable => self::value( $i_xValue->jsonSerialize() ),
            $i_xValue instanceof Throwable => self::value( self::exceptionToArray( $i_xValue ) ),
            $i_xValue instanceof \BackedEnum => $i_xValue::class . '( ' . $i_xValue->name . ': ' . $i_xValue->value . ')',
            $i_xValue instanceof \UnitEnum => $i_xValue::class . '( ' . $i_xValue->name . ')',
            $i_xValue instanceof Stringable => $i_xValue::class . '(' . $i_xValue->__toString() . ')',
            default => self::valueObject( $i_xValue, $i_nuPropertyCount ),
        };
        if ( is_string( $x ) ) {
            $x = str_replace( [ "\t", "\n", "\r", chr( 0 ) ], [ '\\t', '\\n', '\\r', '\\0' ], $x );
        }
        return $x;
    }


    public static function valueObject( object $i_obj, ?int $i_nuPropertyCount ) : string {
        if ( method_exists( $i_obj, '__debugInfo' ) ) {
            $rProperties = $i_obj->__debugInfo();
        } else {
            $rProperties = get_object_vars( $i_obj );
        }
        return self::valueObjectProperties( $i_obj::class, spl_object_id( $i_obj ), $rProperties, $i_nuPropertyCount );
    }


    /**
     * @param array<string, mixed> $i_r
     * @param int                  $i_uIndent      The number of spaces to indent nested arrays. (Internal.)
     * @param list<mixed[]|object> $i_rAlreadySeen Objects that have already been printed. (Internal.)
     * @return string The formatted string representation of the array.
     */
    private static function formatArrayInner( array $i_r, int $i_uIndent,
                                              array &$i_rAlreadySeen = [] ) : string {
        $stIndent = str_repeat( ' ', $i_uIndent );
        $st = "{\n";
        foreach ( $i_r as $stKey => $xValue ) {
            $st .= "{$stIndent}  {$stKey}: ";
            if ( is_array( $xValue ) ) {
                if ( in_array( $xValue, $i_rAlreadySeen, true ) ) {
                    $st .= "array (already printed)\n";
                } else {
                    $i_rAlreadySeen[] = $xValue;
                    $st .= 'array ' . self::formatArrayInner( $xValue, $i_uIndent + 2, $i_rAlreadySeen );
                }
            } elseif ( is_object( $xValue ) ) {
                if ( in_array( $xValue, $i_rAlreadySeen, true ) ) {
                    $st .= get_class( $xValue ) . " (already printed)\n";
                } else {
                    $i_rAlreadySeen[] = $xValue;
                    $st .= get_class( $xValue ) . ' ' . self::formatArrayInner( (array) $xValue, $i_uIndent + 2, $i_rAlreadySeen );
                }
            } elseif ( is_bool( $xValue ) ) {
                $st .= $xValue ? 'true' : 'false';
                $st .= "\n";
            } elseif ( is_null( $xValue ) ) {
                $st .= 'null';
                $st .= "\n";
            } else {
                $st .= $xValue;
                $st .= "\n";
            }
        }
        $st .= "{$stIndent}}\n";
        return $st;
    }


    /** @param array<int|string, mixed> $i_rProperties */
    private static function valueObjectProperties( string $i_stClass, ?int $i_nuID, array $i_rProperties,
                                                   ?int   $i_nuPropertyCount ) : string {

        $bTooMany = is_int( $i_nuPropertyCount ) && count( $i_rProperties ) > $i_nuPropertyCount;
        if ( $bTooMany ) {
            $i_rProperties = array_slice( $i_rProperties, 0, $i_nuPropertyCount );
        }
        $rOut = [];
        $uCount = 0;
        foreach ( $i_rProperties as $k => $v ) {
            $rOut[] = "{$k}:{$v}";
            if ( $bTooMany && ++$uCount >= $i_nuPropertyCount ) {
                break;
            }
        }
        if ( $bTooMany ) {
            $rOut[] = '...';
        }
        $stProperties = implode( ', ', $rOut );
        if ( is_int( $i_nuID ) ) {
            $i_stClass .= "#{$i_nuID}";
        }
        if ( $stProperties ) {
            $i_stClass .= "({$stProperties})";
        }
        return $i_stClass;
    }


}
