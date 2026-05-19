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


    /** @param array<int|string, mixed> $context
     * @noinspection PhpCastIsUnnecessaryInspection
     */
    public static function interpolate( string|Stringable $message, array $context ) : string {
        $replace = [];
        foreach ( $context as $key => $val ) {
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

        # interpolate replacement values into the message and return
        return strtr( strval( $message ), $replace );
    }


    /**
     * @param mixed $xValue The input value.
     * @return mixed The loggable value.
     *
     * Prepare an arbitrary value to be represented in a log context without
     * converting it to a string. I.e., after running this, the returned value
     * will be a scalar type, a string, or an array (possible nested) of scalar
     * types or strings.
     */
    public static function value( mixed $xValue ) : mixed {
        if ( is_int( $xValue ) || is_float( $xValue ) || is_bool( $xValue ) || is_null( $xValue )
            || is_string( $xValue ) ) {
            return $xValue;
        }
        if ( is_array( $xValue ) ) {
            return array_map( fn( $x ) => self::value( $x ), $xValue );
        }
        if ( is_resource( $xValue ) ) {
            return '(resource)';
        }

        if ( ! is_object( $xValue ) ) {
            // @codeCoverageIgnoreStart
            $stType = gettype( $xValue );
            $stValue = strval( $xValue );
            return "{$stType}({$stValue})";
            // @codeCoverageIgnoreEnd
        }
        if ( $xValue instanceof ContextSerializable ) {
            return self::value( $xValue->contextSerialize() );
        }
        if ( $xValue instanceof JsonSerializable ) {
            return self::value( $xValue->jsonSerialize() );
        }
        if ( $xValue instanceof Throwable ) {
            return self::value( self::exceptionToArray( $xValue ) );
        }
        if ( $xValue instanceof Stringable ) {
            return strval( $xValue );
        }
        return $xValue::class;
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


}
