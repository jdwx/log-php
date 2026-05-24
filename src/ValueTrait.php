<?php


declare( strict_types = 1 );


namespace JDWX\Log;


trait ValueTrait {


    private int $uDepth = LogTools::DEFAULT_DEPTH;

    private int $uPropertyCount = LogTools::DEFAULT_PROPERTY_COUNT;


    public function getDepth() : int {
        return $this->uDepth;
    }


    public function getPropertyCount() : int {
        return $this->uPropertyCount;
    }


    public function setDepth( int $i_nDepth ) : void {
        $this->uDepth = $i_nDepth;
    }


    public function setPropertyCount( int $i_nPropertyCount ) : void {
        $this->uPropertyCount = $i_nPropertyCount;
    }


    public function value( mixed          $x, false|int|null $i_nuDepth = false,
                           false|int|null $i_nuPropertyCount = false ) : mixed {
        $uDepth = false === $i_nuDepth ? $this->uDepth : ( $i_nuDepth ?? PHP_INT_MAX );
        $uPropertyCount = false === $i_nuPropertyCount ? $this->uPropertyCount : $i_nuPropertyCount;
        return LogTools::value( $x, $uDepth, $uPropertyCount );
    }


}