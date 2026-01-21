<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use DateTimeZone;
use JDWX\Log\BufferLogger;
use JDWX\Log\TimestampLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( TimestampLogger::class )]
final class TimestampLoggerTest extends TestCase {


    public function testLog() : void {
        $stToday = gmdate( 'Y-m-d' );
        $buffer = new BufferLogger();
        $logger = new TimestampLogger( $buffer );
        $logger->log( LOG_DEBUG, 'foo' );
        $log = $buffer->shiftLogEx();
        self::assertSame( LogLevel::DEBUG, $log->level );
        self::assertStringContainsString( 'foo', $log->message );
        self::assertStringContainsString( $stToday, $log->message );
    }


    public function testLogWithTimezoneString() : void {
        $buffer = new BufferLogger();
        $logger = new TimestampLogger( $buffer, '[Y-m-d H:i:s P] ', 'America/New_York' );
        $logger->log( LOG_DEBUG, 'foo' );
        $log = $buffer->shiftLogEx();
        self::assertStringContainsString( 'foo', $log->message );
        # New York is either -05:00 or -04:00 depending on DST
        self::assertMatchesRegularExpression( '/-0[45]:00/', $log->message );
    }


    public function testLogWithTimezoneObject() : void {
        $buffer = new BufferLogger();
        $tz = new DateTimeZone( 'Europe/London' );
        $logger = new TimestampLogger( $buffer, '[Y-m-d H:i:s P] ', $tz );
        $logger->log( LOG_DEBUG, 'foo' );
        $log = $buffer->shiftLogEx();
        self::assertStringContainsString( 'foo', $log->message );
        # London is either +00:00 or +01:00 depending on DST
        self::assertMatchesRegularExpression( '/\+0[01]:00/', $log->message );
    }


    public function testLogWithLocalTimezone() : void {
        $buffer = new BufferLogger();
        $logger = new TimestampLogger( $buffer, '[Y-m-d H:i:s] ', null );
        $logger->log( LOG_DEBUG, 'foo' );
        $log = $buffer->shiftLogEx();
        self::assertStringContainsString( 'foo', $log->message );
        # Just verify it has a timestamp format - local time varies
        self::assertMatchesRegularExpression( '/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] foo$/', $log->message );
    }


}
