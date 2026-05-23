<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\GlobalContext;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( GlobalContext::class )]
final class GlobalContextTest extends TestCase {


    public function testArrayAccess() : void {
        $gtx = new GlobalContext();
        self::assertFalse( isset( $gtx[ 'foo' ] ) );
        $gtx[ 'foo' ] = 'bar';
        self::assertTrue( isset( $gtx[ 'foo' ] ) );
        self::assertSame( 'bar', $gtx[ 'foo' ] );

        unset( $gtx[ 'foo' ] );
        self::assertFalse( isset( $gtx[ 'foo' ] ) );

        self::assertNull( $gtx[ 'foo' ] );

        self::assertNull( $gtx[ 'bar' ] );
    }


    public function testJsonSerialize() : void {
        $gtx = new GlobalContext();
        $gtx[ 'foo' ] = [ 'bar' => 'baz' ];
        $gtx[ 'qux' ] = 'quux';
        $r = $gtx->jsonSerialize();
        self::assertCount( 2, $r );
        self::assertSame( [ 'bar' => 'baz' ], $r[ 'foo' ] );
        self::assertSame( 'quux', $r[ 'qux' ] );
    }


}
