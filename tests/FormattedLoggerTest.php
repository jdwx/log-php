<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\FormattedLogger;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use RuntimeException;
use stdClass;


require_once __DIR__ . '/MyFormattedLogger.php';


#[CoversClass( FormattedLogger::class )]
final class FormattedLoggerTest extends TestCase {


    public function testExceptionToArray() : void {
        $ex = new RuntimeException( 'TEST_MESSAGE', 0, new LogicException( 'INNER_MESSAGE', 1 ) );
        $exArray = FormattedLogger::exceptionToArray( $ex );
        self::assertStringContainsString( 'TEST_MESSAGE', $exArray[ 'message' ] );
        self::assertStringContainsString( 'INNER_MESSAGE', $exArray[ 'previous' ][ 'message' ] );
        self::assertSame( 0, $exArray[ 'code' ] );
        self::assertSame( 1, $exArray[ 'previous' ][ 'code' ] );
    }


    public function testFormatArray() : void {
        $result = FormattedLogger::formatArray( [
            'message' => 'TEST_MESSAGE',
            'foo' => 'bar',
            'baz' => true,
            'qux' => false,
            'quux' => null,
        ] );
        self::assertStringContainsString( 'message: TEST_MESSAGE', $result );
        self::assertStringContainsString( 'foo: bar', $result );
        self::assertStringContainsString( 'baz: true', $result );
        self::assertStringContainsString( 'qux: false', $result );
        self::assertStringContainsString( 'quux: null', $result );
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


    public function testInterpolate() : void {
        self::assertSame( 'Test {y}', FormattedLogger::interpolate( 'Test {y}', [ 'x' => 123 ] ) );
        self::assertSame( 'Test', FormattedLogger::interpolate( 'Test', [] ) );
        self::assertSame( 'Test 123', FormattedLogger::interpolate( 'Test {x}', [ 'x' => 123 ] ) );
        self::assertSame( 'Test {foo*bar}', FormattedLogger::interpolate( 'Test {foo*bar}', [ 'foo*bar' => 'baz' ] ) );
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
        $this->expectException( InvalidArgumentException::class );
        $logger->log( 'INVALID', 'TEST_MESSAGE' );
    }


    public function testLogNotice() : void {
        $logger = new MyFormattedLogger();
        $logger->notice( 'TEST_MESSAGE' );
        self::assertStringContainsString( 'TEST_MESSAGE', $logger->stWritten );
    }


}
