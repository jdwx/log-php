<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\BufferLogger;
use JDWX\Log\DebugFilterLogger;
use JDWX\Log\FilterLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( FilterLogger::class )]
final class FilterLoggerTest extends TestCase {


    public function testLog() : void {
        $buf = new BufferLogger();
        $logger = new DebugFilterLogger( $buf );
        $logger->log( 'test', 'test message' );
        $log = $buf->shiftLogEx();
        self::assertStringContainsString( 'message', $log->message );

        $logger->log( LOG_DEBUG, 'test message' );
        self::assertNull( $buf->shiftLog() );
    }


}
