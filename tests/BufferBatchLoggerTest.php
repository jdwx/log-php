<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractBatchLogger;
use JDWX\Log\BufferBatchLogger;
use JDWX\Log\GlobalContext;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( AbstractBatchLogger::class )]
#[CoversClass( BufferBatchLogger::class )]
final class BufferBatchLoggerTest extends TestCase {


    public function testBatch() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::CRITICAL, 'foo', [ 'bar' => 'baz' ] );
        $bbl->log( LogLevel::ERROR, 'qux', [ 'quux' => 'corge' ] );
        $bbl->flushLog();
        self::assertSame( LogLevel::CRITICAL, $bbl->stLastLevel );
        self::assertSame( LogLevel::CRITICAL, $bbl->rLastBatch[ 0 ]->level );
        self::assertSame( 'foo', $bbl->rLastBatch[ 0 ]->message );
        self::assertSame( 'baz', $bbl->rLastBatch[ 0 ]->context[ 'bar' ] );
        self::assertSame( LogLevel::ERROR, $bbl->rLastBatch[ 1 ]->level );
        self::assertSame( 'qux', $bbl->rLastBatch[ 1 ]->message );
        self::assertSame( 'corge', $bbl->rLastBatch[ 1 ]->context[ 'quux' ] );
    }


    public function testGlobalContext() : void {
        $gtx = new GlobalContext();
        $bbl = new BufferBatchLogger( $gtx );

        $gtx[ 'foo' ] = 'bar';
        $bbl->log( LogLevel::DEBUG, 'baz', [ 'qux' => 'quux', 'waldo' => 'fred' ] );
        $bbl->log( LogLevel::INFO, 'corge', [ 'qux' => 'quux', 'grault' => 'garply' ] );

        $bbl->flushLog();
        self::assertSame( LogLevel::INFO, $bbl->stLastLevel );

        self::assertSame( 'bar', $bbl->rCommonContext[ 'foo' ] );
        self::assertSame( 'quux', $bbl->rCommonContext[ 'qux' ] );
        self::assertCount( 2, $bbl->rCommonContext );

        self::assertSame( 'fred', $bbl->rLastBatch[ 0 ]->context[ 'waldo' ] );
        self::assertCount( 1, $bbl->rLastBatch[ 1 ]->context );

        self::assertSame( 'garply', $bbl->rLastBatch[ 1 ]->context[ 'grault' ] );
        self::assertCount( 1, $bbl->rLastBatch[ 1 ]->context );

    }


}