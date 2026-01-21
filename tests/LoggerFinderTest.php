<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\BufferLogger;
use JDWX\Log\HasLoggerInterface;
use JDWX\Log\LoggerFinder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;


#[CoversClass( LoggerFinder::class )]
final class LoggerFinderTest extends TestCase {


    public function testGetLoggerForConstructor() : void {
        $buffer = new BufferLogger();
        $finder = new LoggerFinder( $buffer );
        self::assertSame( $buffer, $finder->getLogger() );
    }


    public function testGetLoggerForContainerNotLogger() : void {
        $x = new class implements HasLoggerInterface {


            public function getLogger() : LoggerInterface {
                return new BufferLogger();
            }


        };
        $finder = new LoggerFinder();
        $finder->try( $x );
        self::assertInstanceOf( BufferLogger::class, $finder->getLogger() );
    }


    public function testGetLoggerForContainerReturnsNull() : void {
        $x = new class implements HasLoggerInterface, LoggerInterface {


            use LoggerTrait;


            public function getLogger() : ?LoggerInterface {
                return null;
            }


            public function log( $level, \Stringable|string $message, array $context = [] ) : void {}


        };
        $finder = new LoggerFinder();
        $finder->try( $x );
        self::assertNull( $finder->getLogger() );
    }


    public function testGetLoggerForContainerReturnsSelf() : void {
        $x = new class implements HasLoggerInterface, LoggerInterface {


            use LoggerTrait;


            public function getLogger() : LoggerInterface {
                return $this;
            }


            public function log( $level, \Stringable|string $message, array $context = [] ) : void {}


        };
        $finder = new LoggerFinder();
        $finder->try( $x );
        self::assertSame( $x, $finder->getLogger() );
    }


    public function testGetLoggerForNothing() : void {
        $finder = new LoggerFinder();
        self::assertNull( $finder->getLogger() );
    }


    public function testGetLoggerForSelf() : void {
        $finder = new LoggerFinder();
        $finder->try( $finder );
        self::assertNull( $finder->getLogger() );
    }


    public function testGetLoggerForTry() : void {
        $finder = new LoggerFinder();
        $buffer = new BufferLogger();
        $finder->try( 6 );
        self::assertNull( $finder->getLogger() );
        $finder->try( $this );
        self::assertNull( $finder->getLogger() );
        $finder->try( null );
        self::assertNull( $finder->getLogger() );
        $finder->try( $buffer );
        self::assertSame( $buffer, $finder->getLogger() );
        $finder->try( 6 );
        self::assertSame( $buffer, $finder->getLogger() );
        $finder->try( null );
        self::assertSame( $buffer, $finder->getLogger() );
        $finder->try( $this );
        self::assertSame( $buffer, $finder->getLogger() );
    }


}
