<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\BufferLogger;
use JDWX\Log\ChainLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;


#[CoversClass( ChainLogger::class )]
final class ChainLoggerTest extends TestCase {


    public function testGetLogger() : void {
        $chain = new ChainLogger();
        self::assertNull( $chain->getLogger() );
        $buf = new BufferLogger();
        $chain->push( $buf );
        self::assertSame( $chain, $chain->getLogger() );
    }


    public function testGetLoggerForPsrLogger() : void {
        $logger = new class extends AbstractLogger {


            /** @param array<int|string, mixed> $context */
            public function log( $level, string|Stringable $message, array $context = [] ) : void {}


        };
        $chain = new ChainLogger( $logger );
        self::assertSame( $chain, $chain->getLogger() );
    }


    public function testLog() : void {
        $buf1 = new BufferLogger();
        $buf2 = new BufferLogger();
        $buf3 = new BufferLogger();
        $chain = new ChainLogger( $buf1, [ $buf2, $buf3 ] );
        $chain->info( 'Test {x}', [ 'x' => 123 ] );

        $log = $buf1->shiftLogEx();
        self::assertSame( LogLevel::INFO, $log->level );
        self::assertSame( 'Test {x}', $log->message );
        self::assertSame( [ 'x' => 123 ], $log->context );

        $log = $buf2->shiftLogEx();
        self::assertSame( LogLevel::INFO, $log->level );
        self::assertSame( 'Test {x}', $log->message );
        self::assertSame( [ 'x' => 123 ], $log->context );

        $log = $buf3->shiftLogEx();
        self::assertSame( LogLevel::INFO, $log->level );
        self::assertSame( 'Test {x}', $log->message );
        self::assertSame( [ 'x' => 123 ], $log->context );
    }


}
