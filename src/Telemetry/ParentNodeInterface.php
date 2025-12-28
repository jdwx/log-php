<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


interface ParentNodeInterface extends NodeInterface {


    public function finishChild() : static;


    public function getChild() : ?ChildNodeInterface;


    public function pushChild( ChildNodeInterface $i_child ) : void;


    public function startChild() : ChildNodeInterface;


}