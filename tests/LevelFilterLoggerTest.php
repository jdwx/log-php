<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\BufferLogger;
use JDWX\Log\LevelFilterLogger;
use PHPUnit\Framework\TestCase;


class LevelFilterLoggerTest extends TestCase {


    public function testExact() : void {
        $buf = new BufferLogger();
        $logger = new LevelFilterLogger( $buf, 'warning', true );
        $logger->notice( 'notice' );
        $logger->warning( 'warning' );
        $logger->error( 'error' );
        $le = $buf->shiftLogEx();
        self::assertSame( 'warning', $le->message );
        self::assertNull( $buf->shiftLog() );
    }


    public function testNotExact() : void {
        $buf = new BufferLogger();
        $logger = new LevelFilterLogger( $buf, 'warning', false );
        $logger->notice( 'notice' );
        $logger->warning( 'warning' );
        $logger->error( 'error' );
        $le = $buf->shiftLogEx();
        self::assertSame( 'warning', $le->message );
        $le = $buf->shiftLogEx();
        self::assertSame( 'error', $le->message );
        self::assertNull( $buf->shiftLog() );
    }


}
