<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use JDWX\Json\Json;
use Stringable;


readonly class TimestampedLogEntry extends LogEntry implements ContextSerializable {


    public float $fTimestampMicro;


    public function __construct( int|string $i_level, string|Stringable $i_stMessage, array $i_rContext ) {
        parent::__construct( $i_level, $i_stMessage, $i_rContext );
        $this->fTimestampMicro = microtime( true );
    }


    public static function compare( self $a, self $b ) : int {
        return $a->fTimestampMicro <=> $b->fTimestampMicro;
    }


    public function __toString() : string {
        $st = gmdate( 'Y-m-d H:i:s', (int) $this->fTimestampMicro )
            . sprintf( '.%06d', (int) ( ( $this->fTimestampMicro - (int) $this->fTimestampMicro ) * 1_000_000 ) )
            . ' ' . parent::__toString();
        if ( ! empty( $this->context ) ) {
            $st .= ' ' . Json::encode( $this->context );
        }
        return $st;
    }


    public function contextSerialize() : array|bool|float|int|string|null {
        $r = [
            'timestamp' => $this->fTimestampMicro,
            'level' => $this->level,
            'message' => $this->message,
        ];
        if ( ! empty( $this->context ) ) {
            $r[ 'context' ] = $this->context;
        }
        return $r;
    }


}
