<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use Exception;
use JDWX\Log\FormattedLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;


require_once __DIR__ . '/MyFormattedLogger.php';


#[CoversClass( FormattedLogger::class )]
final class FormattedLoggerTest extends TestCase {


    public function testLog() : void {
        $logger = new MyFormattedLogger();
        $logger->log( LogLevel::WARNING, 'TEST_MESSAGE', [
            'class' => 'TEST_CLASS',
            'code' => 0,
        ] );
        self::assertStringContainsString( 'WARNING', $logger->stWritten );
        self::assertStringContainsString( 'TEST_MESSAGE', $logger->stWritten );
        self::assertStringContainsString( 'TEST_CLASS', $logger->stWritten );
        self::assertStringNotContainsString( '0', $logger->stWritten );
    }


    public function testLogDebug() : void {
        $logger = new MyFormattedLogger();
        $logger->debug( 'TEST_MESSAGE' );
        self::assertStringContainsString( 'TEST_MESSAGE', $logger->stWritten );
    }


    public function testLogForEmptyContext() : void {
        $logger = new MyFormattedLogger();
        $logger->log( LogLevel::WARNING, 'TEST_MESSAGE' );
        self::assertStringContainsString( 'WARNING', $logger->stWritten );
        self::assertStringContainsString( 'TEST_MESSAGE', $logger->stWritten );
    }


    public function testLogInfo() : void {
        $logger = new MyFormattedLogger();
        $logger->info( 'TEST_MESSAGE' );
        self::assertStringContainsString( 'TEST_MESSAGE', $logger->stWritten );
    }


    public function testLogInvalid() : void {
        $logger = new MyFormattedLogger();
        $this->expectException( InvalidArgumentException::class );
        $logger->log( 'INVALID', 'TEST_MESSAGE' );
    }


    public function testLogNotice() : void {
        $logger = new MyFormattedLogger();
        $logger->notice( 'TEST_MESSAGE' );
        self::assertStringContainsString( 'TEST_MESSAGE', $logger->stWritten );
    }


    public function testRenderContext() : void {
        $logger = new MyFormattedLogger();
        $result = $logger->renderContext( [ 'exception' => new Exception( 'Test exception' ) ] );
        self::assertStringContainsString( 'class: Exception', $result );
    }


}
