<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests\Telemetry;


use JDWX\Log\Telemetry\ParentNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( ParentNode::class )]
final class ParentNodeTest extends TestCase {


    public function testAddContext() : void {
        $tx = new ParentNode();
        $tx->addContext( [
            'foo' => 'bar',
            'baz' => 42,
        ] );
        $child = $tx->startChild();
        $tx->addContext( [
            'qux' => 'quux',
        ] );
        $tx->finishChild();
        $tx->addContext( [
            'corge' => 'grault',
        ] );
        $r = $tx->getContext();
        self::assertSame( [
            'foo' => 'bar',
            'baz' => 42,
            'corge' => 'grault',
        ], $r );
        self::assertSame( [
            'qux' => 'quux',
        ], $child->getContext() );
    }


    public function testGetChild() : void {
        $tx = new ParentNode();
        self::assertNull( $tx->getChild() );
        $child = $tx->startChild();
        self::assertSame( $child, $tx->getChild() );
        $tx->finishChild();
        self::assertNull( $tx->getChild() );
    }


    public function testLog() : void {
        $tx = new ParentNode();
        $tx->info( 'Info message before child.', [ 'foo' => 'bar' ] );
        $child = $tx->startChild();
        $tx->info( 'Info message in child.', [ 'foo' => 'bar' ] );
        $child->finish();
        $tx->info( 'Info message after child.', [ 'foo' => 'baz' ] );
        $r = $tx->contextSerialize();
        $rChild = $r[ 1 ];
        assert( is_array( $rChild ) );
        $logChild = $rChild[ 0 ];
        assert( is_array( $logChild ) );
        self::assertSame( 'Info message in child.', $logChild[ 'message' ] );
        self::assertSame( LogLevel::INFO, $logChild[ 'level' ] );
        self::assertSame( 'bar', $logChild[ 'context' ][ 'foo' ] );
        $log = $r[ 2 ];
        assert( is_array( $log ) );
        self::assertSame( 'Info message after child.', $log[ 'message' ] );
        self::assertSame( LogLevel::INFO, $log[ 'level' ] );
        self::assertSame( 'baz', $log[ 'context' ][ 'foo' ] );
    }


    public function testSetContextWithChild() : void {
        $tx = new ParentNode();
        $tx->startChild();
        $tx->setContext( 'foo', 'bar' );
        $r = $tx->contextSerialize();
        $childContext = $r[ 0 ];
        assert( is_array( $childContext ) );
        self::assertSame( 'bar', $childContext[ 'foo' ] );
    }


}
