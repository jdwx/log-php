<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\LogEntry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( LogEntry::class )]
class LogEntryTest extends TestCase {


    public function testConstructForAlert() : void {
        $logEntry = new LogEntry( LOG_ALERT, 'test', [] );
        self::assertSame( 'test', $logEntry->message );
        self::assertSame( LogLevel::ALERT, $logEntry->level );
    }


    public function testConstructForCritical() : void {
        $logEntry = new LogEntry( LOG_CRIT, 'test', [] );
        self::assertSame( 'test', $logEntry->message );
        self::assertSame( LogLevel::CRITICAL, $logEntry->level );
    }


    public function testConstructForDebug() : void {
        $logEntry = new LogEntry( LOG_DEBUG, 'test', [] );
        self::assertSame( 'test', $logEntry->message );
        self::assertSame( LogLevel::DEBUG, $logEntry->level );
    }


    public function testConstructForEmergency() : void {
        $logEntry = new LogEntry( LOG_EMERG, 'test', [] );
        self::assertSame( 'test', $logEntry->message );
        self::assertSame( LogLevel::EMERGENCY, $logEntry->level );
    }


    public function testConstructForError() : void {
        $logEntry = new LogEntry( LOG_ERR, 'test', [] );
        self::assertSame( 'test', $logEntry->message );
        self::assertSame( LogLevel::ERROR, $logEntry->level );
    }


    public function testConstructForInfo() : void {
        $logEntry = new LogEntry( LOG_INFO, 'test', [] );
        self::assertSame( 'test', $logEntry->message );
        self::assertSame( LogLevel::INFO, $logEntry->level );
    }


    public function testConstructForInvalidLevel() : void {
        $logEntry = new LogEntry( 'test', 'test', [] );
        self::assertSame( 'INVALID(test)', $logEntry->level );
    }


    public function testConstructForNotice() : void {
        $logEntry = new LogEntry( LOG_NOTICE, 'test', [] );
        self::assertSame( 'test', $logEntry->message );
        self::assertSame( LogLevel::NOTICE, $logEntry->level );
    }


    public function testConstructForWarning() : void {
        $logEntry = new LogEntry( LOG_WARNING, 'test', [] );
        self::assertSame( 'test', $logEntry->message );
        self::assertSame( LogLevel::WARNING, $logEntry->level );
    }


    public function testContext() : void {
        $context = [ 'key' => 'value' ];
        $logEntry = new LogEntry( LOG_INFO, 'test', $context );
        self::assertSame( $context, $logEntry->context() );
    }


    public function testInterpolatedMessage() : void {
        $logEntry = new LogEntry( LOG_INFO, 'foo-{bar}-baz', [
            'bar' => 'qux',
        ] );
        self::assertSame( 'foo-qux-baz', $logEntry->interpolatedMessage() );
    }


    public function testJsonSerialize() : void {
        $logEntry = new LogEntry( LOG_INFO, 'foo-{bar}-baz', [ 'bar' => 'qux' ] );
        $jso = $logEntry->jsonSerialize();
        self::assertSame( LogLevel::INFO, $jso[ 'level' ] );
        self::assertSame( 'foo-qux-baz', $jso[ 'message' ] );
        self::assertSame( [ 'bar' => 'qux' ], $jso[ 'context' ] );
    }


    public function testLevel() : void {
        $logEntry = new LogEntry( LOG_INFO, 'test', [] );
        self::assertSame( LogLevel::INFO, $logEntry->level() );

        $logEntry = new LogEntry( LogLevel::ERROR, 'test', [] );
        self::assertSame( LogLevel::ERROR, $logEntry->level() );
    }


    public function testMessage() : void {
        $logEntry = new LogEntry( LOG_INFO, 'test message', [] );
        self::assertSame( 'test message', $logEntry->message() );
    }


    public function testToString() : void {
        $logEntry = new LogEntry( LOG_INFO, 'test message', [] );
        self::assertSame( '[info] test message', strval( $logEntry ) );
    }


    public function testWithContext() : void {
        $logEntry = new LogEntry( LOG_INFO, 'test message', [] );
        $logEntry = $logEntry->withContext( [ 'foo' => 'bar' ] );
        self::assertSame( LogLevel::INFO, $logEntry->level() );
        self::assertSame( 'test message', $logEntry->message() );
        self::assertSame( [ 'foo' => 'bar' ], $logEntry->context() );
    }


}
