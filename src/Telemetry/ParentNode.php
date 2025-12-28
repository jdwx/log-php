<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


use JDWX\Log\ContextSerializable;


class ParentNode extends Node implements ParentNodeInterface {


    private ?ChildNodeInterface $activeChild = null;


    public function addContext( array $i_rContext ) : void {
        if ( $this->activeChild instanceof ChildNodeInterface ) {
            $this->activeChild->addContext( $i_rContext );
            return;
        }
        parent::addContext( $i_rContext );
    }


    public function finishChild() : static {
        $this->activeChild = null;
        return $this;
    }


    public function getChild() : ?ChildNodeInterface {
        return $this->activeChild;
    }


    public function log( $level, \Stringable|string $message, array $context = [] ) : void {
        if ( $this->activeChild instanceof ChildNodeInterface ) {
            $this->activeChild->log( $level, $message, $context );
            return;
        }
        parent::log( $level, $message, $context );
    }


    public function pushChild( ChildNodeInterface $i_child ) : void {
        $this->push( $i_child );
        $this->activeChild = $i_child;
    }


    /** @param mixed[]|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null $i_value */
    public function setContext( string $i_stKey, array|bool|float|int|string|ContextSerializable|\JsonSerializable|\Stringable|null $i_value ) : void {
        if ( $this->activeChild instanceof ChildNodeInterface ) {
            $this->activeChild->setContext( $i_stKey, $i_value );
            return;
        }
        parent::setContext( $i_stKey, $i_value );
    }


    public function startChild() : ChildNodeInterface {
        $child = new ChildNode( $this );
        $this->pushChild( $child );
        return $child;
    }


}
