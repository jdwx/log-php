<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests\Telemetry;


use JDWX\Log\Telemetry\ChildNode;
use JDWX\Log\Telemetry\StringTransaction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ChildNode::class )]
final class SpanTest extends TestCase {


    public function testSetContext() : void {
        $tx = new StringTransaction();
        $span = $tx->startChild();
        $span->setContext( 'foo', 'bar' );
        $r = $span->getContext();
        self::assertSame( 'bar', $r[ 'foo' ] );
    }


}
