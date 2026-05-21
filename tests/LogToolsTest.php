<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use Exception;
use JDWX\Log\ContextSerializable;
use JDWX\Log\LogTools;
use JsonException;
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


    public function testExceptionToArrayForTooDeep() : void {
        $ex3 = new JsonException( 'foo', 0 );
        $ex2 = new LogicException( 'foo', 1, $ex3 );
        $ex = new RuntimeException( 'bar', 2, $ex2 );

        $r = LogTools::exceptionToArray( $ex, 2 );
        self::assertSame( 'RuntimeException', $r[ 'class' ] );
        self::assertSame( 'LogicException', $r[ 'previous' ][ 'class' ] );
        self::assertStringStartsWith( 'JsonException#', $r[ 'previous' ][ 'previous' ] );
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
        self::assertStringContainsString( '...', $result );
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
        self::assertStringContainsString( 'x: stdClass#', $result );
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
        self::assertStringContainsString( 'x: stdClass#', $result );
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
        self::assertStringContainsString( 'x: stdClass#', $result );
    }


    public function testFormatForArray() : void {
        $r = [ 'foo' => 'bar' ];
        $st = LogTools::format( $r );
        self::assertStringStartsWith( '{', $st );
        self::assertStringContainsString( 'foo: bar', $st );
        self::assertStringEndsWith( "}\n", $st );
    }


    public function testFormatForBool() : void {
        self::assertSame( 'true', LogTools::format( true ) );
        self::assertSame( 'false', LogTools::format( false ) );
    }


    public function testFormatForInt() : void {
        self::assertSame( '1', LogTools::format( 1 ) );
    }


    public function testFormatForNull() : void {
        self::assertSame( 'null', LogTools::format( null ) );
    }


    public function testFormatForObject() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $st = LogTools::format( $x );
        self::assertStringStartsWith( 'stdClass#', $st );
        self::assertStringContainsString( 'foo: bar', $st );
    }


    public function testFormatForResource() : void {
        $f = fopen( 'php://memory', 'r+' );
        self::assertStringStartsWith( 'stream(', LogTools::format( $f ) );
    }


    public function testFormatObject() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $x->baz = [ new stdClass(), 'qux' ];
        $x->baz[ 0 ]->quux = 'corge';
        $st = LogTools::formatObject( $x );
        self::assertStringStartsWith( 'stdClass#', $st );
        self::assertStringContainsString( 'foo: bar', $st );
        self::assertStringContainsString( 'object$class: stdClass', $st );
        self::assertStringContainsString( 'baz: array', $st );
        self::assertStringContainsString( 'quux: corge', $st );
        self::assertStringContainsString( '1: qux', $st );
    }


    public function testInterpolate() : void {
        self::assertSame( 'Test {y}', LogTools::interpolate( 'Test {y}', [ 'x' => 123 ] ) );
        self::assertSame( 'Test', LogTools::interpolate( 'Test', [] ) );
        self::assertSame( 'Test 123', LogTools::interpolate( 'Test {x}', [ 'x' => 123 ] ) );
        self::assertSame( 'Test {foo*bar}', LogTools::interpolate( 'Test {foo*bar}', [ 'foo*bar' => 'baz' ] ) );
    }


    public function testLimitArrayForNoDepth() : void {
        $r = LogTools::value( [ 1, 2, 3, 4, 5 ], 0 );
        self::assertSame( [ '...' ], $r );
    }


    public function testLimitArrayForTooMany() : void {
        $r = LogTools::value( [ 1, 2, 3, 4, 5 ], 2, 3 );
        self::assertSame( [ 1, 2, 3, '...' ], $r );
    }


    public function testObjectAsArrayForNoDepth() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $x->baz = 'qux';
        $r = LogTools::objectAsArray( $x, 0 );
        self::assertSame( 'stdClass', $r[ 'object$class' ] );
        self::assertIsInt( $r[ 'object$id' ] );
        self::assertCount( 2, $r );
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


    public function testValueForCircularReference() : void {
        $x1 = new stdClass();
        $x2 = new stdClass();
        $x1->x2 = $x2;
        $x2->x1 = $x1;
        $r = LogTools::value( $x1 );
        self::assertSame( 'stdClass', $r[ 'object$class' ] );
        self::assertSame( 'stdClass', $r[ 'x2' ][ 'object$class' ] );
        self::assertStringStartsWith( 'stdClass#', $r[ 'x2' ][ 'x1' ] );
    }


    public function testValueForContextSerializable() : void {
        $ctx = new class implements ContextSerializable {


            public function contextSerialize() : string {
                return 'test';
            }


        };
        self::assertSame( 'test', LogTools::value( $ctx ) );
    }


    public function testValueForContextSerializableNested() : void {
        $foo = new class implements ContextSerializable {


            public ContextSerializable $foo;


            /** @return array<string, mixed> */
            public function contextSerialize() : array {
                return [ 'foo' => $this->foo ];
            }


        };

        $bar = new class implements ContextSerializable {


            public ContextSerializable $bar;


            /** @return array<string, mixed> */
            public function contextSerialize() : array {
                return [ 'bar' => $this->bar ];
            }


        };
        $foo->foo = $bar;
        $bar->bar = $foo;
        $r = LogTools::value( $foo );
        self::assertStringStartsWith( 'JDWX\\Log\\ContextSerializable@anonymous\\0', $r[ 'foo' ][ 'bar' ] );
        self::assertStringContainsString( '\\0' . __FILE__, $r[ 'foo' ][ 'bar' ] );
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


    public function testValueForJsonSerializableNested() : void {
        $foo = new class implements JsonSerializable {


            public JsonSerializable $foo;


            /** @return array<string, object> */
            public function jsonSerialize() : array {
                return [ 'foo' => $this->foo ];
            }


        };

        $bar = new class implements JsonSerializable {


            public JsonSerializable $bar;


            /** @return array<string, object> */
            public function jsonSerialize() : array {
                return [ 'bar' => $this->bar ];
            }


        };

        $foo->foo = $bar;
        $bar->bar = $foo;

        $r = LogTools::value( $foo );
        self::assertStringStartsWith( 'JsonSerializable@anonymous\\0', $r[ 'foo' ][ 'bar' ] );
        self::assertStringContainsString( '\\0' . __FILE__, $r[ 'foo' ][ 'bar' ] );

    }


    public function testValueForNestedObjects() : void {
        $str = new class implements Stringable {


            public function __toString() : string {
                return 'bar';
            }


        };
        $obj = new stdClass();
        $obj->foo = $str;
        $r = LogTools::value( $obj );
        self::assertSame( 'stdClass', $r[ 'object$class' ] );
        self::assertStringContainsString( 'Stringable@anonymous\0', $r[ 'foo' ] );
        self::assertStringContainsString( '\0' . __FILE__, $r[ 'foo' ] );
        self::assertStringContainsString( '(bar)', $r[ 'foo' ] );
    }


    public function testValueForObject() : void {
        $st = LogTools::value( $this );
        self::assertSame( $this::class . '#' . spl_object_id( $this ), $st );
    }


    public function testValueForObjectNoDepth() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $x->baz = 'qux';
        self::assertStringStartsWith( 'stdClass#', LogTools::value( $x, 0 ) );
    }


    public function testValueForObjectWithDebugInfo() : void {
        $x = new class {


            public function __debugInfo() : array {
                return [
                    'foo' => 'bar',
                    'baz' => 'qux',
                    'quux' => 'corge',
                    'grault' => 'garply',
                ];
            }


        };
        $r = LogTools::value( $x, 3, 2 );
        self::assertStringContainsString( 'class@anonymous', $r[ 'object$class' ] );
        self::assertStringContainsString( '\\0' . __FILE__ . ':', $r[ 'object$class' ] );
        self::assertSame( 'bar', $r[ 'foo' ] );
        self::assertSame( 'qux', $r[ 'baz' ] );
        self::assertContains( '...', $r );
        self::assertCount( 5, $r );
    }


    public function testValueForResource() : void {
        $f = fopen( 'php://memory', 'r' );
        self::assertStringContainsString( 'stream(', LogTools::value( $f ) );
    }


    public function testValueForStringable() : void {
        $str = new class implements Stringable {


            public function __toString() : string {
                return 'test';
            }


        };

        $st = LogTools::value( $str );
        self::assertStringContainsString( "Stringable@anonymous\\0" . __FILE__, $st );
        self::assertStringContainsString( '(test)', $st );
    }


}
