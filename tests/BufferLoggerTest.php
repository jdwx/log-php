<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use Exception;
use JDWX\Log\AbstractLogger;
use JDWX\Log\BufferLogger;
use JDWX\Log\LogEntry;
use JDWX\Log\LoggerExtraTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use RuntimeException;


#[CoversClass( AbstractLogger::class )]
#[CoversClass( BufferLogger::class )]
#[CoversClass( LoggerExtraTrait::class )]
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


    public function testAlertFromEx() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->alertFromEx( $ex );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::ALERT, $logEntry->level );
        self::assertSame( 'foo', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


    public function testAlertFromExWithMessage() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->alertFromEx( $ex, 'bar' );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::ALERT, $logEntry->level );
        self::assertSame( 'bar', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
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


    public function testCriticalFromEx() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->criticalFromEx( $ex );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::CRITICAL, $logEntry->level );
        self::assertSame( 'foo', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


    public function testCriticalFromExWithMessage() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->criticalFromEx( $ex, 'bar' );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::CRITICAL, $logEntry->level );
        self::assertSame( 'bar', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
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


    public function testDebugFromEx() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->debugFromEx( $ex );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::DEBUG, $logEntry->level );
        self::assertSame( 'foo', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


    public function testDebugFromExWithMessage() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->debugFromEx( $ex, 'bar' );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::DEBUG, $logEntry->level );
        self::assertSame( 'bar', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
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


    public function testEmergencyFromEx() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->emergencyFromEx( $ex );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::EMERGENCY, $logEntry->level );
        self::assertSame( 'foo', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


    public function testEmergencyFromExWithMessage() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->emergencyFromEx( $ex, 'bar' );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::EMERGENCY, $logEntry->level );
        self::assertSame( 'bar', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
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


    public function testErrorFromEx() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->errorFromEx( $ex );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::ERROR, $logEntry->level );
        self::assertSame( 'foo', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


    public function testErrorFromExWithMessage() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->errorFromEx( $ex, 'bar' );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::ERROR, $logEntry->level );
        self::assertSame( 'bar', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
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


    public function testInfoFromEx() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->infoFromEx( $ex );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::INFO, $logEntry->level );
        self::assertSame( 'foo', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


    public function testInfoFromExWithMessage() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->errorFromEx( $ex, 'bar' );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::ERROR, $logEntry->level );
        self::assertSame( 'bar', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
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


    public function testNoticeFromEx() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->noticeFromEx( $ex );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::NOTICE, $logEntry->level );
        self::assertSame( 'foo', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


    public function testNoticeFromExWithMessage() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->noticeFromEx( $ex, 'bar' );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::NOTICE, $logEntry->level );
        self::assertSame( 'bar', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


    public function testShiftLog() : void {
        $log = new BufferLogger();
        self::assertNull( $log->shiftLog() );
        $log->alert( 'TEST_ALERT' );
        self::assertCount( 1, $log );
        $shifted = $log->shiftLog();
        assert( $shifted instanceof LogEntry );
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
        $this->expectException( RuntimeException::class );
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


    public function testWarningFromEx() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->warningFromEx( $ex );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::WARNING, $logEntry->level );
        self::assertSame( 'foo', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


    public function testWarningFromExWithMessage() : void {
        $ex = new Exception( 'foo' );
        $logger = new BufferLogger();
        $logger->warningFromEx( $ex, 'bar' );
        $logEntry = $logger->shiftLogEx();
        self::assertSame( LogLevel::WARNING, $logEntry->level );
        self::assertSame( 'bar', $logEntry->message );
        self::assertSame( 'foo', $logEntry->context[ 'exception' ][ 'message' ] );
    }


}
