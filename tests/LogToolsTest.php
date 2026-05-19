<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use Exception;
use JDWX\Log\ContextSerializable;
use JDWX\Log\LogTools;
use JsonSerializable;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Stringable;


#[CoversClass( LogTools::class )]
final class LogToolsTest extends TestCase {


    public function testExceptionToArray() : void {
        $ex = new RuntimeException( 'TEST_MESSAGE', 0, new LogicException( 'INNER_MESSAGE', 1 ) );
        $exArray = LogTools::exceptionToArray( $ex );
        self::assertStringContainsString( 'TEST_MESSAGE', $exArray[ 'message' ] );
        self::assertStringContainsString( 'INNER_MESSAGE', $exArray[ 'previous' ][ 'message' ] );
        self::assertSame( 0, $exArray[ 'code' ] );
        self::assertSame( 1, $exArray[ 'previous' ][ 'code' ] );
    }


    public function testFormatArray() : void {
        $result = LogTools::formatArray( [
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
        $result = LogTools::formatArray( $x );
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
        $result = LogTools::formatArray( [ 'x' => $x ] );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
        self::assertStringContainsString( 'qux', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


    public function testFormatArrayForNestedArray() : void {
        $result = LogTools::formatArray( [ 'message' => 'TEST_MESSAGE', 'foo' => [ 'bar' => 'baz' ] ] );
        self::assertStringContainsString( 'TEST_MESSAGE', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
    }


    public function testFormatArrayForObject() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $result = LogTools::formatArray( [ 'x' => $x ] );
        self::assertStringContainsString( 'stdClass', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
    }


    public function testFormatArrayForObjectArrayLoop() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $r = [ 'x' => $x ];
        $x->r = $r;
        $result = LogTools::formatArray( $r );
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
        $result = LogTools::formatArray( [ 'x' => $x ] );
        self::assertStringContainsString( 'stdClass', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
        self::assertStringContainsString( 'qux', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


    public function testInterpolate() : void {
        self::assertSame( 'Test {y}', LogTools::interpolate( 'Test {y}', [ 'x' => 123 ] ) );
        self::assertSame( 'Test', LogTools::interpolate( 'Test', [] ) );
        self::assertSame( 'Test 123', LogTools::interpolate( 'Test {x}', [ 'x' => 123 ] ) );
        self::assertSame( 'Test {foo*bar}', LogTools::interpolate( 'Test {foo*bar}', [ 'foo*bar' => 'baz' ] ) );
    }


    public function testValueForArray() : void {
        $r = [ 0 => 'foo', 1 => 'bar', 2 => true, 'baz' => 'qux' ];
        self::assertSame( $r, LogTools::value( $r ) );
    }


    public function testValueForBuiltin() : void {
        self::assertSame( 'test', LogTools::value( 'test' ) );
        self::assertSame( true, LogTools::value( true ) );
        self::assertSame( null, LogTools::value( null ) );
        self::assertSame( 12345, LogTools::value( 12345 ) );
        self::assertSame( 12345.6, LogTools::value( 12345.6 ) );
    }


    public function testValueForContextSerializable() : void {
        $ctx = new class implements ContextSerializable {


            public function contextSerialize() : string {
                return 'test';
            }


        };
        self::assertSame( 'test', LogTools::value( $ctx ) );
    }


    public function testValueForException() : void {
        $ex = new Exception( 'Test exception', 12345 );
        $rCheck = [
            'class' => 'Exception',
            'message' => 'Test exception',
            'code' => 12345,
            'file' => __FILE__,
            'line' => -1,
            'trace' => '#trace',
        ];
        $rValue = LogTools::value( $ex );
        $rValue[ 'line' ] = -1;
        $rValue[ 'trace' ] = '#trace';
        self::assertSame( $rCheck, $rValue );
    }


    public function testValueForJsonSerializable() : void {
        $jso = new class implements JsonSerializable {


            public function jsonSerialize() : string {
                return 'test';
            }


        };
        self::assertSame( 'test', LogTools::value( $jso ) );
    }


    public function testValueForObject() : void {
        self::assertSame( $this::class, LogTools::value( $this ) );
    }


    public function testValueForResource() : void {
        $f = fopen( 'php://memory', 'r' );
        self::assertSame( '(resource)', LogTools::value( $f ) );
    }


    public function testValueForStringable() : void {
        $str = new class implements Stringable {


            public function __toString() : string {
                return 'test';
            }


        };

        self::assertSame( 'test', LogTools::value( $str ) );
    }


}
