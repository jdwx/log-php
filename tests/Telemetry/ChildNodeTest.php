<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests\Telemetry;


use JDWX\Log\Telemetry\ChildNode;
use JDWX\Log\Telemetry\ParentNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ChildNode::class )]
final class ChildNodeTest extends TestCase {


    public function testFinish() : void {
        $parent = new ParentNode();
        $child = new ChildNode( $parent );
        $parent->pushChild( $child );
        self::assertSame( $child, $parent->getChild() );
        $child->finish();
        self::assertNull( $parent->getChild() );
    }


    public function testParent() : void {
        $tx = new ParentNode();
        $child = new ChildNode( $tx );
        $tx2 = $child->parent();
        self::assertSame( $tx, $tx2 );
    }


}
