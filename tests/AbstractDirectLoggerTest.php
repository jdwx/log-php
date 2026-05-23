<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractDirectLogger;
use JDWX\Log\GlobalContext;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractDirectLogger::class )]
final class AbstractDirectLoggerTest extends TestCase {


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


    private function newLogger( ?GlobalContext $gtx = null ) : AbstractDirectLogger {
        return new class( $gtx ) extends AbstractDirectLogger {


            public function log( $level, \Stringable|string $message, array $context = [] ) : void {
                // TODO: Implement log() method.
            }


        };
    }


}
