<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractProxyLogger;
use JDWX\Log\BufferLogger;
use JDWX\Log\ProxyLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;


#[CoversClass( AbstractProxyLogger::class )]
#[CoversClass( ProxyLogger::class )]
final class ProxyLoggerTest extends TestCase {


    public function testGetLogger() : void {
        $log = new BufferLogger();
        $proxy = new ProxyLogger();

        self::assertSame( null, $proxy->getLogger() );
        $proxy->setLogger( $log );
        self::assertSame( $log, $proxy->getLogger() );
    }


    public function testHasLogger() : void {
        $proxy = new ProxyLogger();
        self::assertFalse( $proxy->hasLogger() );

        $log = new BufferLogger();
        $proxy->setLogger( $log );
        self::assertTrue( $proxy->hasLogger() );
    }


    public function testLog() : void {
        $buffer = new BufferLogger();
        $proxy = new ProxyLogger();
        $proxy->setLogger( $buffer );
        $proxy->log( LogLevel::INFO, 'foo', [ 'baz' => 'qux' ] );
        $entry = $buffer->shiftLogEx();
        self::assertSame( LogLevel::INFO, $entry->level() );
        self::assertSame( 'foo', $entry->message() );
        self::assertSame( 'qux', $entry->context()[ 'baz' ] );
    }


    public function testLogForDecoration() : void {
        $log = new class extends AbstractLogger {


            public string $message = 'bar';


            public function log( $level, string|Stringable $message, array $context = [] ) : void {
                $this->message = (string) $message;
            }


        };
        $deco = new ProxyLogger( $log );
        $ex = new \RuntimeException( 'foo' );
        $deco->warningFromEx( $ex );
        self::assertSame( 'foo', $log->message );
    }


    public function testSetLogger() : void {
        $proxy = new ProxyLogger();
        self::assertNull( $proxy->getLogger() );

        $log = new class extends AbstractLogger {


            public function log( $level, string|Stringable $message, array $context = [] ) : void {}


        };
        $proxy->setLogger( $log );
        self::assertSame( $log, $proxy->getLogger() );
    }


}
