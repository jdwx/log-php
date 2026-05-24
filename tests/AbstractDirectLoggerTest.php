<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractDirectLogger;
use JDWX\Log\GlobalContext;
use JDWX\Log\LogTools;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractDirectLogger::class )]
final class AbstractDirectLoggerTest extends TestCase {


    public function testDepth() : void {
        $logger = $this->newLogger();
        self::assertSame( LogTools::DEFAULT_DEPTH, $logger->getDepth() );
        $logger->setDepth( 123 );
        self::assertSame( 123, $logger->getDepth() );

        $gtx = new GlobalContext();
        $gtx->setDepth( 234 );
        $logger = $this->newLogger( $gtx );
        self::assertSame( 234, $logger->getDepth() );

    }


    public function testGetGlobalContext() : void {
        $log = $this->newLogger();
        self::assertNull( $log->getGlobalContext() );

        $gtx = new GlobalContext();
        $log = $this->newLogger( $gtx );
        self::assertSame( $gtx, $log->getGlobalContext() );
    }


    public function testGetLogger() : void {
        $log = $this->newLogger();
        self::assertSame( $log, $log->getLogger() );
    }


    public function testHasLogger() : void {
        $log = $this->newLogger();
        self::assertTrue( $log->hasLogger() );
    }


    public function testPropertyCount() : void {
        $logger = $this->newLogger();
        self::assertSame( LogTools::DEFAULT_PROPERTY_COUNT, $logger->getPropertyCount() );
        $logger->setPropertyCount( 123 );
        self::assertSame( 123, $logger->getPropertyCount() );

        $gtx = new GlobalContext();
        $gtx->setPropertyCount( 234 );
        $logger = $this->newLogger( $gtx );
        self::assertSame( 234, $logger->getPropertyCount() );
    }


    public function testValueForDepth() : void {

        $rInput = [ 'foo' => [ 'bar' => [ 'baz' => [ 'qux' => 'quux' ] ] ] ];

        $logger = $this->newLogger();
        $rOutput = $logger->value( $rInput );
        self::assertSame( [ 'foo' => [ 'bar' => [ 'baz' => [ '...' ] ] ] ], $rOutput );

        $logger->setDepth( 2 );
        $rOutput = $logger->value( $rInput );
        self::assertSame( [ 'foo' => [ 'bar' => [ '...' ] ] ], $rOutput );

        $rOutput = $logger->value( $rInput, 1 );
        self::assertSame( [ 'foo' => [ '...' ] ], $rOutput );

        $rOutput = $logger->value( $rInput, null );
        self::assertSame( $rInput, $rOutput );

    }


    public function testValueForPropertyCount() : void {

        $rInput = [ 'foo', 'bar', 'baz', 'qux', 'quux', 'corge', 'grault' ];

        $logger = $this->newLogger();
        $rOutput = $logger->value( $rInput );
        self::assertSame( [ 'foo', 'bar', 'baz', 'qux', 'quux', 'corge', '...' ], $rOutput );

        $logger->setPropertyCount( 3 );
        $rOutput = $logger->value( $rInput );
        self::assertSame( [ 'foo', 'bar', 'baz', '...' ], $rOutput );

        $rOutput = $logger->value( $rInput, i_nuPropertyCount: 2 );
        self::assertSame( [ 'foo', 'bar', '...' ], $rOutput );

    }


    private function newLogger( ?GlobalContext $gtx = null ) : AbstractDirectLogger {
        return new class( $gtx ) extends AbstractDirectLogger {


            public function log( $level, \Stringable|string $message, array $context = [] ) : void {
                // TODO: Implement log() method.
            }


        };
    }


}
