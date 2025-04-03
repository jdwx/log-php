<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerTrait;
use Stringable;


abstract class FormattedLogger extends AbstractLogger {


    use LoggerTrait;


    /** @return array<string, mixed> */
    public static function exceptionToArray( \Throwable $x ) : array {
        $r = [
            'class' => get_class( $x ),
            'message' => $x->getMessage(),
            'code' => $x->getCode(),
            'file' => $x->getFile(),
            'line' => $x->getLine(),
            'trace' => $x->getTraceAsString(),
        ];
        $prev = $x->getPrevious();
        if ( $prev instanceof \Throwable ) {
            $r[ 'previous' ] = static::exceptionToArray( $prev );
        }
        return $r;
    }


    /** @param array<string, mixed> $i_r */
    public static function formatArray( array $i_r ) : string {
        return self::formatArrayInner( $i_r, 0 );
    }


    /**
     * @param array<string, mixed> $i_r
     * @param int $i_uIndent The number of spaces to indent nested arrays. (Internal.)
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
            } else {
                $st .= $xValue;
                $st .= "\n";
            }
        }
        $st .= "{$stIndent}}\n";
        return $st;
    }


    /**
     * @inheritDoc
     */
    public function log( mixed $level, Stringable|string $message, array $context = [] ) : void {
        $stLevel = $this->renderLevel( $level, $context );
        $stMessage = $this->renderMessage( $message );
        $stContext = $this->renderContext( $context );
        $this->write( trim( "{$stLevel}: {$stMessage} {$stContext}" ) );
    }


    /** @param mixed[] $context */
    public function renderContext( array $context ) : string {
        if ( empty( $context ) ) {
            return '';
        }
        if ( isset( $context[ 'code' ] ) && 0 === $context[ 'code' ] ) {
            unset( $context[ 'code' ] );
        }
        return static::formatArray( $context );
    }


    /** @param mixed[] $context */
    protected function renderLevel( mixed $level, array &$context ) : string {
        $stLevel = strtoupper( static::normalizeLevelEx( $level, 'UNKNOWN' ) );
        if ( isset( $context[ 'class' ] ) ) {
            $stLevel .= '(' . $context[ 'class' ] . ')';
            unset( $context[ 'class' ] );
        }
        return $stLevel;
    }


    protected function renderMessage( string|Stringable $stMessage ) : string {
        return $stMessage instanceof Stringable ? $stMessage->__toString() : $stMessage;
    }


    abstract protected function write( string $stMessage ) : void;


}
