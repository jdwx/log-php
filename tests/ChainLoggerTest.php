<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\BufferLogger;
use JDWX\Log\ChainLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( ChainLogger::class )]
final class ChainLoggerTest extends TestCase {


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
