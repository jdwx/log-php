<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\TimestampedLogEntry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( TimestampedLogEntry::class )]
final class TimestampedLogEntryTest extends TestCase {


    public function testCompare() : void {
        $log1 = new TimestampedLogEntry( 'info', 'Message 1', [] );
        usleep( 100 ); // Ensure a different timestamp
        $log2 = new TimestampedLogEntry( 'info', 'Message 2', [] );

        self::assertLessThan( 0, TimestampedLogEntry::compare( $log1, $log2 ) );
        self::assertGreaterThan( 0, TimestampedLogEntry::compare( $log2, $log1 ) );
        self::assertSame( 0, TimestampedLogEntry::compare( $log1, $log1 ) );
    }


    public function testContextSerialize() : void {
        $tmBefore = microtime( true );
        $log = new TimestampedLogEntry( 'info', 'Test message', [ 'foo' => 'bar' ] );
        $tmAfter = microtime( true );
        $r = $log->contextSerialize();
        assert( is_array( $r ) );
        self::assertLessThanOrEqual( $tmAfter, $r[ 'timestamp' ] );
        self::assertGreaterThanOrEqual( $tmBefore, $r[ 'timestamp' ] );
        self::assertSame( 'info', $r[ 'level' ] );
        self::assertSame( 'Test message', $r[ 'message' ] );
        self::assertSame( [ 'foo' => 'bar' ], $r[ 'context' ] );
    }


    public function testToString() : void {
        $log = new TimestampedLogEntry( 'info', 'Test message', [ 'foo' => 'bar' ] );
        $st = strval( $log );
        self::assertStringContainsString( '[info]', $st );
        self::assertStringContainsString( 'Test message', $st );
        self::assertStringContainsString( '{"foo":"bar"}', $st );
    }


}
