<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\BufferLogger;
use JDWX\Log\LoggerDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


class LoggerDecoratorTest extends TestCase {


    public function testGetLogger() : void {
        $log = new BufferLogger();
        $deco = new LoggerDecorator( $log );
        self::assertSame( $log, $deco->getLogger() );
    }


    public function testLog() : void {
        $log = new BufferLogger();
        $deco = new LoggerDecorator( $log );
        $deco->log( LogLevel::INFO, 'foo', [ 'bar' => 'baz' ] );
        $logEntry = $log->shiftLogEx();
        self::assertSame( LogLevel::INFO, $logEntry->level );
        self::assertSame( 'foo', $logEntry->message );
        self::assertSame( 'baz', $logEntry->context[ 'bar' ] );
    }


}