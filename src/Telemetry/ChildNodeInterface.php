<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


interface ChildNodeInterface extends NodeInterface {


    public function finish() : ParentNodeInterface;


}