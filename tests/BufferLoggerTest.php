<?php


declare( strict_types = 1 );


use JDWX\Log\BufferLogger;
use JDWX\Log\LogEntry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( BufferLogger::class )]
final class BufferLoggerTest extends TestCase {


    public function testAlert() : void {
        $log = new BufferLogger();
        $rContext = [ 'foo' => 'bar' ];
        $log->alert( 'Test', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLogEx();
        self::assertSame( LogLevel::ALERT, $log->level );
        self::assertSame( 'Test', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testCount() : void {
        $logger = new BufferLogger();
        self::assertSame( 0, $logger->count() );
        $logger->alert( 'TEST_ALERT' );
        self::assertSame( 1, $logger->count() );
        $logger->warning( 'TEST_WARNING' );
        self::assertSame( 2, $logger->count() );
    }


    public function testCritical() : void {
        $log = new BufferLogger();
        $rContext = [ 'foo' => 'bar' ];
        $log->critical( 'Test', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLogEx();
        self::assertSame( LogLevel::CRITICAL, $log->level );
        self::assertSame( 'Test', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testDebug() : void {
        $log = new BufferLogger();
        $rContext = [ 'foo' => 'bar' ];
        $log->debug( 'Test', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLogEx();
        self::assertSame( LogLevel::DEBUG, $log->level );
        self::assertSame( 'Test', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testEmergency() : void {
        $log = new BufferLogger();
        $rContext = [ 'foo' => 'bar' ];
        $log->emergency( 'Test', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLogEx();
        self::assertSame( LogLevel::EMERGENCY, $log->level );
        self::assertSame( 'Test', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testEmpty() : void {
        $logger = new BufferLogger();
        self::assertTrue( $logger->empty() );
        $logger->alert( 'TEST_ALERT' );
        self::assertFalse( $logger->empty() );
        $logger->shiftLogEx();
        self::assertTrue( $logger->empty() );
    }


    public function testError() : void {
        $log = new BufferLogger();
        $rContext = [ 'foo' => 'bar' ];
        $log->error( 'Test', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLogEx();
        self::assertSame( LogLevel::ERROR, $log->level );
        self::assertSame( 'Test', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testInfo() : void {
        $log = new BufferLogger();
        $rContext = [ 'foo' => 'bar' ];
        $log->info( 'Test', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLogEx();
        self::assertSame( LogLevel::INFO, $log->level );
        self::assertSame( 'Test', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testLog() : void {
        $log = new BufferLogger();
        $rContext = [ 'foo' => 'bar' ];
        $log->log( LogLevel::DEBUG, 'Test', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLogEx();
        self::assertSame( LogLevel::DEBUG, $log->level );
        self::assertSame( 'Test', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testNotice() : void {
        $log = new BufferLogger();
        $rContext = [ 'foo' => 'bar' ];
        $log->notice( 'Test', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLogEx();
        self::assertSame( LogLevel::NOTICE, $log->level );
        self::assertSame( 'Test', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testShiftLog() : void {
        $log = new BufferLogger();
        self::assertNull( $log->shiftLog() );
        $log->alert( 'TEST_ALERT' );
        self::assertCount( 1, $log );
        $shifted = $log->shiftLog();
        self::assertInstanceOf( LogEntry::class, $shifted );
        self::assertSame( LogLevel::ALERT, $shifted->level );
        self::assertSame( 'TEST_ALERT', $shifted->message );
        self::assertNull( $log->shiftLog() );
    }


    public function testShiftLogEx() : void {
        $log = new BufferLogger();
        $log->alert( 'TEST_ALERT' );
        self::assertCount( 1, $log );
        $shifted = $log->shiftLogEx();
        self::assertSame( LogLevel::ALERT, $shifted->level );
        $this->expectException( \RuntimeException::class );
        $log->shiftLogEx();
    }


    public function testWarning() : void {
        $log = new BufferLogger();
        $rContext = [ 'foo' => 'bar' ];
        $log->warning( 'Test', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLogEx();
        self::assertSame( LogLevel::WARNING, $log->level );
        self::assertSame( 'Test', $log->message );
        self::assertSame( $rContext, $log->context );
    }


}
