<?php


declare( strict_types = 1 );


namespace JDWX\Log;


trait ReadOnlyValueTrait {


    private readonly int $uDepth;

    private readonly int $uPropertyCount;


    public function getDepth() : int {
        return $this->uDepth;
    }


    public function getPropertyCount() : int {
        return $this->uPropertyCount;
    }


    public function value( mixed          $x, false|int|null $i_nuDepth = false,
                           false|int|null $i_nuPropertyCount = false ) : mixed {
        /** @phpstan-ignore property.uninitializedReadonly */
        $uDepth = false === $i_nuDepth ? $this->uDepth : ( $i_nuDepth ?? PHP_INT_MAX );
        /** @phpstan-ignore property.uninitializedReadonly */
        $uPropertyCount = false === $i_nuPropertyCount ? $this->uPropertyCount : $i_nuPropertyCount;
        return LogTools::value( $x, $uDepth, $uPropertyCount );
    }


    /**
     * @param GlobalContext|null $gtx
     * @return void
     * @suppress PhanAccessReadOnlyProperty
     *
     * Because these properties are read only, this method must be
     * called from the constructor.
     */
    private function fromGlobalContext( ?GlobalContext $gtx ) : void {
        // @phpstan-ignore property.readOnlyAssignNotInConstructor
        $this->uDepth = $gtx?->getDepth() ?? LogTools::DEFAULT_DEPTH;
        // @phpstan-ignore property.readOnlyAssignNotInConstructor
        $this->uPropertyCount = $gtx?->getPropertyCount() ?? LogTools::DEFAULT_PROPERTY_COUNT;
    }


}