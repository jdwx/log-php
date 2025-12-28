<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


class ParentNode extends Node implements ParentNodeInterface {


    private ?NodeInterface $activeChild = null;


    public function finishChild() : static {
        $this->activeChild = null;
        return $this;
    }


    public function log( $level, \Stringable|string $message, array $context = [] ) : void {
        if ( $this->activeChild instanceof ChildNode ) {
            $this->activeChild->log( $level, $message, $context );
            return;
        }
        parent::log( $level, $message, $context );
    }


    public function setContext( string $i_stKey, mixed $i_value ) : void {
        if ( $this->activeChild instanceof ChildNode ) {
            $this->activeChild->setContext( $i_stKey, $i_value );
            return;
        }
        parent::setContext( $i_stKey, $i_value );
    }


    public function startChild() : ChildNodeInterface {
        $this->activeChild = new ChildNode( $this );
        $this->push( $this->activeChild );
        return $this->activeChild;
    }


}
