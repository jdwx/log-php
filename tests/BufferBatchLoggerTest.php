<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractBatchLogger;
use JDWX\Log\BufferBatchLogger;
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


}