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


    public function testLevelToString() : void {
        self::assertSame( LogLevel::INFO, LogEntry::levelToString( LOG_INFO ) );
        self::assertSame( LogLevel::ERROR, LogEntry::levelToString( LOG_ERR ) );
        self::assertSame( LogLevel::DEBUG, LogEntry::levelToString( 'debug' ) );
        self::assertSame( LogLevel::DEBUG, LogEntry::levelToString( 'Debug' ) );
        self::assertSame( LogLevel::DEBUG, LogEntry::levelToString( 'DEBUG' ) );
        self::assertNull( LogEntry::levelToString( 9999 ) );
    }


    public function testLevelToStringEx() : void {
        self::assertSame( LogLevel::INFO, LogEntry::levelToStringEx( LOG_INFO ) );
        self::assertSame( LogLevel::ERROR, LogEntry::levelToStringEx( LOG_ERR ) );
        $this->expectException( \InvalidArgumentException::class );
        LogEntry::levelToStringEx( 9999 );
    }


    public function testToString() : void {
        $logEntry = new LogEntry( LOG_INFO, 'test message', [] );
        self::assertSame( '[info] test message', strval( $logEntry ) );
    }


}
