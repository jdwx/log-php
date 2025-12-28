<?php /** @noinspection PhpAccessStaticViaInstanceInspection */


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\ContextSerializableTrait;
use JDWX\Log\Telemetry\StringTransaction;
use PHPUnit\Framework\TestCase;


final class ContextSerializableTraitTest extends TestCase {


    public function testSerialize() : void {
        $obj = $this->makeObject();
        self::assertTrue( $obj::serialize( true ) );
        self::assertSame( 4.1, $obj::serialize( 4.1 ) );
        self::assertSame( 41, $obj::serialize( 41 ) );
        self::assertSame( 'foo', $obj::serialize( 'foo' ) );

        $tx = new StringTransaction();
        self::assertSame( [], $obj::serialize( $tx ) );

        $jso = new class implements \JsonSerializable {


            public function jsonSerialize() : array {
                return [ 'foo' => 'bar' ];
            }


        };
        self::assertSame( [ 'foo' => 'bar' ], $obj::serialize( $jso ) );

        $str = new class implements \Stringable {


            public function __toString() : string {
                return 'bar';
            }


        };
        self::assertSame( 'bar', $obj::serialize( $str ) );

        self::assertSame( [ 'foo' => 'bar' ], $obj::serialize( [ 'foo' => $str ] ) );
    }


    private function makeObject() : object {
        return new class {


            use ContextSerializableTrait {
                serialize as public;
            }
        };
    }


}
