<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\BufferLogger;
use JDWX\Log\LoggerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( LoggerRegistry::class )]
final class LoggerRegistryTest extends TestCase {


    public function testGetForAlt() : void {
        $buffer = new BufferLogger();
        LoggerRegistry::register( $buffer, 'jdwx.log.alt' );
        self::assertNull( LoggerRegistry::get() );
        self::assertSame( $buffer, LoggerRegistry::get( 'jdwx.log.alt' ) );
    }


    public function testGetForNull() : void {
        self::assertNull( LoggerRegistry::get() );
    }


    public function testGetForNullAlt() : void {
        $buffer = new BufferLogger();
        LoggerRegistry::register( $buffer );
        self::assertSame( $buffer, LoggerRegistry::get() );
        self::assertNull( LoggerRegistry::get( 'jdwx.log.no-such-logger' ) );
    }


    public function testUnregister() : void {
        self::assertNull( LoggerRegistry::get() );
        $buffer = new BufferLogger();
        LoggerRegistry::register( $buffer );
        self::assertSame( $buffer, LoggerRegistry::get() );
        LoggerRegistry::unregister();
        self::assertNull( LoggerRegistry::get() );
    }


    protected function setUp() : void {
        parent::setUp();
        LoggerRegistry::clear();
    }


}
