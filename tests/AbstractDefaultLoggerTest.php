<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractDefaultLogger;
use JDWX\Log\BufferLogger;
use JDWX\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


final class AbstractDefaultLoggerTest extends TestCase {


    public function testGetLogger() : void {
        $lastDitch = $this->newLogger();
        $logger = $lastDitch->getLogger();
        self::assertInstanceOf( BufferLogger::class, $logger );

        # Make sure it doesn't make a new one every time.
        self::assertSame( $logger, $lastDitch->getLogger() );
    }


    public function testLog() : void {
        $lastDitch = $this->newLogger();
        $lastDitch->log( LogLevel::INFO, 'foo' );
        $logger1 = $lastDitch->getLogger();
        assert( $logger1 instanceof BufferLogger );
        $log = $logger1->shiftLogEx();
        self::assertEquals( 'foo', $log->message() );

        $logger2 = new BufferLogger();
        $lastDitch->setLogger( $logger2 );

        $lastDitch->log( LogLevel::DEBUG, 'bar' );
        self::assertNull( $logger1->shiftLog() );

        $log = $logger2->shiftLogEx();
        self::assertEquals( 'bar', $log->message() );
    }


    private function newLogger() : AbstractDefaultLogger {
        return new class extends AbstractDefaultLogger {


            protected function newDefaultLogger() : LoggerInterface {
                return new BufferLogger();
            }


        };
    }


}
