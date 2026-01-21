<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


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


}
