<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


interface ParentNodeInterface extends NodeInterface {


    public function finishChild() : static;


}