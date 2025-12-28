<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests\Telemetry;


use JDWX\Log\Telemetry\Node;
use JDWX\Log\Telemetry\StringTransaction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( Node::class )]
final class NodeTest extends TestCase {


    public function testAddContext() : void {
        $node = new Node();
        $node->addContext( [
            'foo' => 'bar',
            'baz' => 42,
        ] );
        $r = $node->getContext();
        self::assertSame( [
            'foo' => 'bar',
            'baz' => 42,
        ], $r );
    }


    public function testLog() : void {
        $node = new Node();
        $node->info( 'Info message.', [ 'foo' => 'bar' ] );
        $r = $node->contextSerialize();
        assert( is_array( $r ) );
        $log = $r[ 0 ];
        assert( is_array( $log ) );
        self::assertSame( 'Info message.', $log[ 'message' ] );
        self::assertSame( LogLevel::INFO, $log[ 'level' ] );
        self::assertSame( 'bar', $log[ 'context' ][ 'foo' ] );
    }


    public function testLogWithSpan() : void {
        $tx = new StringTransaction();
        $tx->startChild();
        $tx->info( 'Info message in span.', [ 'foo' => 'bar' ] );
        $tx->finish();
        $r = $tx->contextSerialize();
        assert( is_array( $r ) );
        $log = $r[ 0 ][ 0 ];
        assert( is_array( $log ) );
        self::assertSame( 'Info message in span.', $log[ 'message' ] );
        self::assertSame( LogLevel::INFO, $log[ 'level' ] );
        self::assertSame( 'bar', $log[ 'context' ][ 'foo' ] );
    }


    public function testPush() : void {
        $node = new Node();
        $node->push( 1 );
        $node->push( true );
        $node->push( 'foo' );
        $node->push( null );
        $node->push( [ 'bar', 2 ] );
        $node->push( 1.23 );

        $child = new Node();
        $child->setContext( 'baz', 'qux' );
        $node->push( $child );

        $expected = [
            1,
            true,
            'foo',
            null,
            [ 'bar', 2 ],
            1.23,
            [ 'baz' => 'qux' ],
        ];
        self::assertSame( $expected, $node->contextSerialize() );
    }


    public function testSetContext() : void {
        $node = new Node();
        $node->setContext( 'foo', 'bar' );
        $r = $node->getContext();
        self::assertSame( 'bar', $r[ 'foo' ] );
    }


    public function testSetContextOverwrites() : void {
        $node = new Node();
        $node->setContext( 'foo', 'bar' );
        $node->setContext( 'foo', 'baz' );
        $r = $node->contextSerialize();
        self::assertSame( 'baz', $r[ 'foo' ] );
    }


}
