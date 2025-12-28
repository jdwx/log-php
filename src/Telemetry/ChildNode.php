<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


class ChildNode extends Node implements ChildNodeInterface {


    public function __construct( private readonly ParentNodeInterface $parent ) {
        parent::__construct();
    }


    public function finish() : void {
        $this->parent()->finishChild();
        parent::finish();
    }


    public function parent() : ParentNodeInterface {
        return $this->parent;
    }


}
