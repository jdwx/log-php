<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\BufferLogger;
use JDWX\Log\LoggerContainer;
use JDWX\Log\LoggerDecorator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;


#[CoversClass( LoggerContainer::class )]
final class LoggerContainerTest extends TestCase {


    public function testGetLogger() : void {
        $log = new BufferLogger();
        $container = new LoggerContainer();

        self::assertSame( null, $container->getLogger() );
        $container->setLogger( $log );
        self::assertSame( $log, $container->getLogger() );
    }


    public function testHasLogger() : void {
        $container = new LoggerContainer();
        self::assertFalse( $container->hasLogger() );

        $log = new BufferLogger();
        $container->setLogger( $log );
        self::assertTrue( $container->hasLogger() );
    }


    public function testLog() : void {
        $buffer = new BufferLogger();
        $container = new LoggerContainer();
        $container->setLogger( $buffer );
        $container->log( LogLevel::INFO, 'foo', [ 'baz' => 'qux' ] );
        $entry = $buffer->shiftLogEx();
        self::assertSame( LogLevel::INFO, $entry->level() );
        self::assertSame( 'foo', $entry->message() );
        self::assertSame( 'qux', $entry->context()[ 'baz' ] );
    }


    public function testSetLoggerWithPsrInterface() : void {
        $log = new class extends AbstractLogger {


            public function log( $level, string|Stringable $message, array $context = [] ) : void {}


        };

        $container = new LoggerContainer();
        $container->setLogger( $log );
        $decorator = $container->getLogger();
        self::assertNotSame( $log, $decorator );
        assert( $decorator instanceof LoggerDecorator );
        self::assertSame( $log, $decorator->getLogger() );
    }


}
