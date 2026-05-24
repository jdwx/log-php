<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractBatchLogger;
use JDWX\Log\BufferBatchLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( AbstractBatchLogger::class )]
final class AbstractBatchLoggerTest extends TestCase {


    public function testDestruct() : void {
        $rMessages = [];
        $stLevel = 'nope';
        $rCommonContext = [];
        $abl = new class( $stLevel, $rMessages, $rCommonContext ) extends AbstractBatchLogger {


            /**
             * @noinspection PhpPropertyOnlyWrittenInspection
             * @phpstan-ignore-next-line
             */
            public function __construct( private string &$stLevel, private array &$rMessage, private array &$rCommonContext ) {
                parent::__construct();
            }


            protected function batch( string $i_stLevel, array $i_rLogMessages, array $i_rCommonContext ) : void {
                $this->stLevel = $i_stLevel;
                $this->rMessage = $i_rLogMessages;
                $this->rCommonContext = $i_rCommonContext;
            }


        };
        $abl->log( LogLevel::INFO, 'foo', [ 'bar' => 'baz' ] );
        unset( $abl );
        self::assertSame( LogLevel::INFO, $stLevel );
        self::assertSame( LogLevel::INFO, $rMessages[ 0 ]->level );
        self::assertSame( 'foo', $rMessages[ 0 ]->message );
        self::assertSame( [], $rMessages[ 0 ]->context );
        self::assertSame( LogLevel::DEBUG, $rMessages[ 1 ]->level );
        self::assertStringContainsString( 'destruct', $rMessages[ 1 ]->message );
        self::assertArrayHasKey( 'class', $rMessages[ 1 ]->context );
        self::assertSame( 'baz', $rCommonContext[ 'bar' ] );
    }


    public function testExtractCommonContextDisabled() : void {
        $bbl = new BufferBatchLogger();
        $bbl->setUseCommonContext( false );
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123, 'host' => 'foo' ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'pid' => 123, 'host' => 'foo' ] );
        $bbl->flushLog();
        self::assertSame( [], $bbl->rCommonContext );
        self::assertSame( [ 'pid' => 123, 'host' => 'foo' ], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [ 'pid' => 123, 'host' => 'foo' ], $bbl->rLastBatch[ 1 ]->context );
    }


    public function testExtractCommonContextDistinguishesNullFromMissingKey() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123, 'session' => null ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'pid' => 123 ] );
        $bbl->flushLog();
        self::assertSame( [ 'pid' => 123 ], $bbl->rCommonContext );
        self::assertSame( [ 'session' => null ], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [], $bbl->rLastBatch[ 1 ]->context );
    }


    public function testExtractCommonContextDistinguishesZeroFromFalseFromEmptyString() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'x' => 0 ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'x' => false ] );
        $bbl->log( LogLevel::INFO, 'third', [ 'x' => '' ] );
        $bbl->flushLog();
        self::assertSame( [], $bbl->rCommonContext );
        self::assertSame( [ 'x' => 0 ], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [ 'x' => false ], $bbl->rLastBatch[ 1 ]->context );
        self::assertSame( [ 'x' => '' ], $bbl->rLastBatch[ 2 ]->context );
    }


    public function testExtractCommonContextOnDestruct() : void {
        $rEntries = [];
        $rCommonContext = [];
        $stLevel = 'nope';
        $abl = new class( $stLevel, $rEntries, $rCommonContext ) extends AbstractBatchLogger {


            /**
             * @noinspection PhpPropertyOnlyWrittenInspection
             * @phpstan-ignore-next-line
             */
            public function __construct( private string &$stLevel, private array &$rEntries, private array &$rCommonContext ) {
                parent::__construct();
            }


            protected function batch( string $i_stLevel, array $i_rLogMessages, array $i_rCommonContext ) : void {
                $this->stLevel = $i_stLevel;
                $this->rEntries = $i_rLogMessages;
                $this->rCommonContext = $i_rCommonContext;
            }


        };
        $abl->log( LogLevel::INFO, 'first', [ 'pid' => 123, 'host' => 'foo' ] );
        $abl->log( LogLevel::INFO, 'second', [ 'pid' => 123, 'host' => 'foo' ] );
        unset( $abl );
        self::assertSame( [ 'pid' => 123, 'host' => 'foo' ], $rCommonContext );
        self::assertSame( [], $rEntries[ 0 ]->context );
        self::assertSame( [], $rEntries[ 1 ]->context );
        self::assertArrayHasKey( 'class', $rEntries[ 2 ]->context );
    }


    public function testExtractCommonContextPreservesEntryLevelAndMessage() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123, 'a' => 1 ] );
        $bbl->log( LogLevel::WARNING, 'second', [ 'pid' => 123, 'b' => 2 ] );
        $bbl->flushLog();
        self::assertSame( LogLevel::INFO, $bbl->rLastBatch[ 0 ]->level );
        self::assertSame( 'first', $bbl->rLastBatch[ 0 ]->message );
        self::assertSame( LogLevel::WARNING, $bbl->rLastBatch[ 1 ]->level );
        self::assertSame( 'second', $bbl->rLastBatch[ 1 ]->message );
    }


    public function testExtractCommonContextStripsCommonKeysFromEntries() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123, 'host' => 'foo', 'a' => 1 ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'pid' => 123, 'host' => 'foo', 'b' => 2 ] );
        $bbl->flushLog();
        self::assertSame( [ 'pid' => 123, 'host' => 'foo' ], $bbl->rCommonContext );
        self::assertSame( [ 'a' => 1 ], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [ 'b' => 2 ], $bbl->rLastBatch[ 1 ]->context );
    }


    public function testExtractCommonContextWithDivergingArrayValuesIsNotCommon() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'tags' => [ 'a', 'b' ] ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'tags' => [ 'a', 'c' ] ] );
        $bbl->flushLog();
        self::assertSame( [], $bbl->rCommonContext );
    }


    public function testExtractCommonContextWithDivergingValuesIsNotCommon() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123, 'host' => 'foo' ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'pid' => 123, 'host' => 'bar' ] );
        $bbl->flushLog();
        self::assertSame( [ 'pid' => 123 ], $bbl->rCommonContext );
        self::assertSame( [ 'host' => 'foo' ], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [ 'host' => 'bar' ], $bbl->rLastBatch[ 1 ]->context );
    }


    public function testExtractCommonContextWithEmptyContexts() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first' );
        $bbl->log( LogLevel::INFO, 'second' );
        $bbl->flushLog();
        self::assertSame( [], $bbl->rCommonContext );
        self::assertSame( [], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [], $bbl->rLastBatch[ 1 ]->context );
    }


    public function testExtractCommonContextWithKeyMissingFromSomeEntriesIsNotCommon() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123, 'host' => 'foo' ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'pid' => 123 ] );
        $bbl->log( LogLevel::INFO, 'third', [ 'pid' => 123, 'host' => 'foo' ] );
        $bbl->flushLog();
        self::assertSame( [ 'pid' => 123 ], $bbl->rCommonContext );
        self::assertSame( [ 'host' => 'foo' ], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [], $bbl->rLastBatch[ 1 ]->context );
        self::assertSame( [ 'host' => 'foo' ], $bbl->rLastBatch[ 2 ]->context );
    }


    public function testExtractCommonContextWithNoSharedKeys() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'a' => 1 ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'b' => 2 ] );
        $bbl->flushLog();
        self::assertSame( [], $bbl->rCommonContext );
        self::assertSame( [ 'a' => 1 ], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [ 'b' => 2 ], $bbl->rLastBatch[ 1 ]->context );
    }


    public function testExtractCommonContextWithSharedArrayValues() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'tags' => [ 'a', 'b' ] ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'tags' => [ 'a', 'b' ] ] );
        $bbl->flushLog();
        self::assertSame( [ 'tags' => [ 'a', 'b' ] ], $bbl->rCommonContext );
    }


    public function testExtractCommonContextWithSharedFalsyValuesIsCommon() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'a' => 0, 'b' => false, 'c' => '' ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'a' => 0, 'b' => false, 'c' => '' ] );
        $bbl->flushLog();
        self::assertSame( [ 'a' => 0, 'b' => false, 'c' => '' ], $bbl->rCommonContext );
        self::assertSame( [], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [], $bbl->rLastBatch[ 1 ]->context );
    }


    public function testExtractCommonContextWithSharedKeys() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123, 'host' => 'foo' ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'pid' => 123, 'host' => 'foo' ] );
        $bbl->flushLog();
        self::assertSame( [ 'pid' => 123, 'host' => 'foo' ], $bbl->rCommonContext );
    }


    public function testExtractCommonContextWithSharedNullValuesIsCommon() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123, 'session' => null ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'pid' => 123, 'session' => null ] );
        $bbl->flushLog();
        self::assertSame( [ 'pid' => 123, 'session' => null ], $bbl->rCommonContext );
        self::assertSame( [], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [], $bbl->rLastBatch[ 1 ]->context );
    }


    public function testExtractCommonContextWithSingleEntry() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'only', [ 'pid' => 123, 'host' => 'foo' ] );
        $bbl->flushLog();
        self::assertSame( [ 'pid' => 123, 'host' => 'foo' ], $bbl->rCommonContext );
        self::assertSame( [], $bbl->rLastBatch[ 0 ]->context );
    }


    public function testFlushClearsEntries() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123 ] );
        $bbl->flushLog();
        self::assertCount( 1, $bbl->rLastBatch );

        $bbl->rLastBatch = [];
        $bbl->rCommonContext = [];
        $bbl->flushLog();
        self::assertSame( [], $bbl->rLastBatch );
        self::assertSame( [], $bbl->rCommonContext );
    }


    public function testFlushResetsCommonContextBetweenBatches() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::INFO, 'first', [ 'pid' => 123 ] );
        $bbl->log( LogLevel::INFO, 'second', [ 'pid' => 123 ] );
        $bbl->flushLog();
        self::assertSame( [ 'pid' => 123 ], $bbl->rCommonContext );

        $bbl->log( LogLevel::INFO, 'third', [ 'pid' => 999 ] );
        $bbl->log( LogLevel::INFO, 'fourth', [ 'pid' => 999 ] );
        $bbl->flushLog();
        self::assertSame( [ 'pid' => 999 ], $bbl->rCommonContext );
    }


    public function testFlushResetsLevelBetweenBatches() : void {
        $bbl = new BufferBatchLogger();
        $bbl->log( LogLevel::CRITICAL, 'crit' );
        $bbl->flushLog();
        self::assertSame( LogLevel::CRITICAL, $bbl->stLastLevel );

        $bbl->log( LogLevel::INFO, 'info' );
        $bbl->flushLog();
        self::assertSame( LogLevel::INFO, $bbl->stLastLevel );
    }


    public function testLogPreservesPerEntryContextWhenContextExceedsValueLimit() : void {
        $bbl = new BufferBatchLogger();
        $rCommon = [
            'host' => 'brick.home.wheelhouse.org',
            'pid' => 1383,
            'type' => 'members-dev',
            'env' => 'dev',
            '_msg' => 'GET /jdw/accounts/C7E6-49521B0A',
        ];
        $bbl->log( LogLevel::WARNING, 'first', $rCommon + [ 'route' => 'inner' ] );
        $bbl->log( LogLevel::DEBUG, 'second', $rCommon + [ 'status' => 200 ] );
        $bbl->flushLog();
        self::assertSame( $rCommon, $bbl->rCommonContext );
        self::assertSame( [ 'route' => 'inner' ], $bbl->rLastBatch[ 0 ]->context );
        self::assertSame( [ 'status' => 200 ], $bbl->rLastBatch[ 1 ]->context );
    }


}
