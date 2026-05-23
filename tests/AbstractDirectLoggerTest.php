<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractDirectLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractDirectLogger::class )]
final class AbstractDirectLoggerTest extends TestCase {


    public function testGetLogger() : void {
        $log = $this->newLogger();
        self::assertSame( $log, $log->getLogger() );
    }


    public function testHasLogger() : void {
        $log = $this->newLogger();
        self::assertTrue( $log->hasLogger() );
    }


    private function newLogger() : AbstractDirectLogger {
        return new class extends AbstractDirectLogger {


            public function log( $level, \Stringable|string $message, array $context = [] ) : void {
                // TODO: Implement log() method.
            }


        };
    }


}
