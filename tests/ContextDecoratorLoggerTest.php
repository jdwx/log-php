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
final class ContextDecoratorLoggerTest extends TestCase {


    public function testGetContext() : void {
        $buffer = new BufferLogger();
        $decorator = new ContextDecoratorLogger( $buffer, [
            'foo' => 'bar',
            'baz' => [
                'qux' => 'quux',
            ],
        ] );

        self::assertSame( 'bar', $decorator->getContext( 'foo' ) );
        self::assertSame( 'quux', $decorator->getContext( 'baz' )[ 'qux' ] );
        self::assertSame( 'quux', $decorator->getContext( 'baz', 'qux' ) );
        self::assertNull( $decorator->getContext( 'nonexistent' ) );
        self::assertNull( $decorator->getContext( 'foo', 'nonexistent' ) );
        self::assertNull( $decorator->getContext( 'baz', 'nonexistent' ) );
    }


    public function testHasContext() : void {
        $buffer = new BufferLogger();
        $decorator = new ContextDecoratorLogger( $buffer, [
            'foo' => 'bar',
            'baz' => [
                'qux' => 'quux',
            ],
        ] );

        self::assertTrue( $decorator->hasContext( 'foo' ) );
        self::assertTrue( $decorator->hasContext( 'baz', 'qux' ) );
        self::assertFalse( $decorator->hasContext( 'baz', 'quux' ) );
        self::assertFalse( $decorator->hasContext( 'qux' ) );
        self::assertFalse( $decorator->hasContext( 'nonexistent' ) );
        self::assertFalse( $decorator->hasContext( 'foo', 'bar' ) );

    }


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


    public function testMergeContext() : void {
        $buffer = new BufferLogger();
        $decorator = new ContextDecoratorLogger( $buffer );
        $decorator->mergeContext( 'foo', 'bar', 'baz' );
        $decorator->mergeContext( 'foo', 'qux', 'quux' );
        $decorator->log( LogLevel::DEBUG, 'corge' );
        $log = $buffer->shiftLogEx();

        self::assertSame( LogLevel::DEBUG, $log->level );
        self::assertSame( 'corge', $log->message );
        self::assertSame( 'baz', $log->context[ 'foo' ][ 'bar' ] );
        self::assertSame( 'quux', $log->context[ 'foo' ][ 'qux' ] );

    }


    public function testMergeContextForNotArray() : void {
        $decorator = new ContextDecoratorLogger( new BufferLogger(), [ 'foo' => 'bar' ] );
        $this->expectException( \InvalidArgumentException::class );
        $decorator->mergeContext( 'foo', 'baz', 123 );
    }


}
