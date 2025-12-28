<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests\Telemetry;


use JDWX\Log\Telemetry\ParentNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( ParentNode::class )]
final class ParentNodeTest extends TestCase {


    public function testLog() : void {
        $tx = new ParentNode();
        $tx->info( 'Info message before child.', [ 'foo' => 'bar' ] );
        $span = $tx->startChild();
        $tx->info( 'Info message in child.', [ 'foo' => 'bar' ] );
        $span->finish();
        $tx->info( 'Info message after child.', [ 'foo' => 'baz' ] );
        $r = $tx->contextSerialize();
        assert( is_array( $r ) );
        $logSpan = $r[ 1 ][ 0 ];
        assert( is_array( $logSpan ) );
        self::assertSame( 'Info message in child.', $logSpan[ 'message' ] );
        self::assertSame( LogLevel::INFO, $logSpan[ 'level' ] );
        self::assertSame( 'bar', $logSpan[ 'context' ][ 'foo' ] );
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
        assert( is_array( $r ) );
        $spanContext = $r[ 0 ];
        assert( is_array( $spanContext ) );
        self::assertSame( 'bar', $spanContext[ 'foo' ] );
    }


}
