<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractContextDecoratorLogger;
use JDWX\Log\BufferLogger;
use JDWX\Log\ContextDecoratorLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( AbstractContextDecoratorLogger::class )]
#[CoversClass( ContextDecoratorLogger::class )]
final class ContextDecoratorTest extends TestCase {


    public function testLog() : void {
        $buffer = new BufferLogger();
        $decorator = new ContextDecoratorLogger( $buffer );
        $decorator->setContext( 'bar', 'baz' );

        $decorator->log( LogLevel::EMERGENCY, 'foo', [ 'qux' => 'quux' ] );
        $log = $buffer->shiftLogEx();

        self::assertSame( 'foo', $log->message );
        self::assertSame( 'baz', $log->context[ 'bar' ] );
        self::assertSame( 'quux', $log->context[ 'qux' ] );

        $decorator->unsetContext( 'bar' );

        $decorator->log( LogLevel::EMERGENCY, 'foo', [ 'qux' => 'quux' ] );
        $log = $buffer->shiftLogEx();

        self::assertSame( 'foo', $log->message );
        self::assertArrayNotHasKey( 'bar', $log->context );
        self::assertSame( 'quux', $log->context[ 'qux' ] );

    }


}
