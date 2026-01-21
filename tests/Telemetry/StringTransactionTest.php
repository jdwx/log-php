<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests\Telemetry;


use JDWX\Json\Json;
use JDWX\Log\Telemetry\AbstractTransaction;
use JDWX\Log\Telemetry\StringTransaction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( AbstractTransaction::class )]
#[CoversClass( StringTransaction::class )]
final class StringTransactionTest extends TestCase {


    public function testFinish() : void {
        $tx = new StringTransaction();
        self::assertFalse( $tx->isFinished() );
        $tx->finish();
        self::assertTrue( $tx->isFinished() );
    }


    public function testSetContext() : void {
        $tx = new StringTransaction();
        $tx->setContext( 'foo', 'bar' );
        $r = $tx->contextSerialize();
        self::assertSame( 'bar', $r[ 'foo' ] );
    }


    public function testToStringForEmpty() : void {
        $tx = new StringTransaction();
        $st = strval( $tx );
        $r = Json::decode( $st );
        self::assertArrayHasKey( 'startTime', $r );
        self::assertArrayHasKey( 'endTime', $r );
        self::assertArrayHasKey( 'duration', $r );
        self::assertCount( 3, $r );
    }


    public function testToStringWithData() : void {
        $tx = new StringTransaction();
        $tx->setContext( 'foo', 'bar' );
        $tx->setContext( 'count', 42 );
        $tx->warning( 'Test warning.', [ 'baz' => 'qux' ] );
        $st = strval( $tx );
        $r = Json::decodeArray( $st );
        self::assertSame( 'bar', $r[ 'foo' ] );
        self::assertSame( 42, $r[ 'count' ] );
        $rLog = $r[ 0 ];
        assert( is_array( $rLog ) );
        self::assertSame( 'Test warning.', $rLog[ 'message' ] );
        self::assertSame( LogLevel::WARNING, $rLog[ 'level' ] );
        self::assertSame( 'qux', $rLog[ 'context' ][ 'baz' ] );
    }


}
