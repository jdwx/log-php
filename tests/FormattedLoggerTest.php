<?php


declare( strict_types = 1 );


use JDWX\Log\FormattedLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


require_once __DIR__ . '/MyFormattedLogger.php';


#[CoversClass( FormattedLogger::class )]
final class FormattedLoggerTest extends TestCase {


    public function testExceptionToArray() : void {
        $ex = new Exception( 'TEST_MESSAGE', 0, new LogicException( 'INNER_MESSAGE', 1 ) );
        $exArray = FormattedLogger::exceptionToArray( $ex );
        self::assertStringContainsString( 'TEST_MESSAGE', $exArray[ 'message' ] );
        self::assertStringContainsString( 'INNER_MESSAGE', $exArray[ 'previous' ][ 'message' ] );
        self::assertSame( 0, $exArray[ 'code' ] );
        self::assertSame( 1, $exArray[ 'previous' ][ 'code' ] );
    }


    public function testFormatArray() : void {
        $result = FormattedLogger::formatArray( [ 'message' => 'TEST_MESSAGE', 'foo' => 'bar' ] );
        self::assertStringContainsString( 'TEST_MESSAGE', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
    }


    public function testFormatArrayForArrayArrayLoop() : void {
        $x = [ 'foo' => 'bar' ];
        $r = [ 'x' => & $x, 'baz' => 'qux' ];
        $x[ 'r' ] = $r;
        $result = FormattedLogger::formatArray( $x );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
        self::assertStringContainsString( 'qux', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


    public function testFormatArrayForArrayObjectLoop() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $r = [ 'x' => $x, 'baz' => 'qux' ];
        $x->r = $r;
        $result = FormattedLogger::formatArray( [ 'x' => $x ] );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
        self::assertStringContainsString( 'qux', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


    public function testFormatArrayForNestedArray() : void {
        $result = FormattedLogger::formatArray( [ 'message' => 'TEST_MESSAGE', 'foo' => [ 'bar' => 'baz' ] ] );
        self::assertStringContainsString( 'TEST_MESSAGE', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
    }


    public function testFormatArrayForObject() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $result = FormattedLogger::formatArray( [ 'x' => $x ] );
        self::assertStringContainsString( 'stdClass', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
    }


    public function testFormatArrayForObjectArrayLoop() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $r = [ 'x' => $x ];
        $x->r = $r;
        $result = FormattedLogger::formatArray( $r );
        self::assertStringContainsString( 'stdClass', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


    public function testFormatArrayForObjectObjectLoop() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $y = new stdClass();
        $y->baz = 'qux';
        $x->y = $y;
        $y->x = $x;
        $result = FormattedLogger::formatArray( [ 'x' => $x ] );
        self::assertStringContainsString( 'stdClass', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
        self::assertStringContainsString( 'qux', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


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
        $logger->log( 'INVALID', 'TEST_MESSAGE' );
        self::assertStringContainsString( 'UNKNOWN', $logger->stWritten );
        self::assertStringContainsString( 'TEST_MESSAGE', $logger->stWritten );
    }


    public function testLogNotice() : void {
        $logger = new MyFormattedLogger();
        $logger->notice( 'TEST_MESSAGE' );
        self::assertStringContainsString( 'TEST_MESSAGE', $logger->stWritten );
    }


}
