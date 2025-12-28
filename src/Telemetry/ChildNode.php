<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


use JDWX\Log\ContextSerializable;
use JDWX\Log\ContextSerializableTrait;
use JsonSerializable;
use Psr\Log\LoggerTrait;


class ChildNode extends Node implements ChildNodeInterface {


    /** @var list<array|bool|float|int|string|ContextSerializable|JsonSerializable|\Stringable|null> */
    private array $rEntries = [];

    use ContextTrait;

    use ContextSerializableTrait;

    use LoggerTrait;


    public function __construct( private readonly ParentNodeInterface $parent ) {}


    public function finish() : ParentNodeInterface {
        $this->parent->finishChild();
        return $this->parent;
    }


}
