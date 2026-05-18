<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractBatchLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( AbstractBatchLogger::class )]
class AbstractBatchLoggerTest extends TestCase {


    public function testDestruct() : void {
        $rMessages = [];
        $stLevel = 'nope';
        $abl = new class( $stLevel, $rMessages ) extends AbstractBatchLogger {


            /**
             * @noinspection PhpPropertyOnlyWrittenInspection
             * @phpstan-ignore-next-line
             */
            public function __construct( private string &$stLevel, private array &$rMessage ) {
            }


            protected function batch( string $i_stLevel, array $i_rLogMessages ) : void {
                $this->stLevel = $i_stLevel;
                $this->rMessage = $i_rLogMessages;
            }


        };
        $abl->log( LogLevel::INFO, 'foo', [ 'bar' => 'baz' ] );
        unset( $abl );
        self::assertSame( LogLevel::INFO, $stLevel );
        self::assertSame( LogLevel::INFO, $rMessages[ 0 ]->level );
        self::assertSame( 'foo', $rMessages[ 0 ]->message );
        self::assertSame( 'baz', $rMessages[ 0 ]->context[ 'bar' ] );
        self::assertSame( LogLevel::DEBUG, $rMessages[ 1 ]->level );
        self::assertStringContainsString( 'destruct', $rMessages[ 1 ]->message );
        self::assertArrayHasKey( 'class', $rMessages[ 1 ]->context );
    }


}