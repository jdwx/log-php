<?php /** @noinspection PhpAccessStaticViaInstanceInspection */


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\ContextSerializableTrait;
use JDWX\Log\Telemetry\StringTransaction;
use PHPUnit\Framework\TestCase;


final class ContextSerializableTraitTest extends TestCase {


    public function testSerialize() : void {
        $obj = $this->makeObject();
        /** @phpstan-ignore staticMethod.notFound */
        self::assertTrue( $obj::serialize( true ) );
        /** @phpstan-ignore staticMethod.notFound */
        self::assertSame( 4.1, $obj::serialize( 4.1 ) );
        /** @phpstan-ignore staticMethod.notFound */
        self::assertSame( 41, $obj::serialize( 41 ) );
        /** @phpstan-ignore staticMethod.notFound */
        self::assertSame( 'foo', $obj::serialize( 'foo' ) );

        $tx = new StringTransaction();
        /** @phpstan-ignore staticMethod.notFound */
        self::assertSame( [], $obj::serialize( $tx ) );

        $jso = new class implements \JsonSerializable {


            /** @return array<string, string> */
            public function jsonSerialize() : array {
                return [ 'foo' => 'bar' ];
            }


        };
        /** @phpstan-ignore staticMethod.notFound */
        self::assertSame( [ 'foo' => 'bar' ], $obj::serialize( $jso ) );

        $str = new class implements \Stringable {


            public function __toString() : string {
                return 'bar';
            }


        };
        /** @phpstan-ignore staticMethod.notFound */
        self::assertSame( 'bar', $obj::serialize( $str ) );

        /** @phpstan-ignore staticMethod.notFound */
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
