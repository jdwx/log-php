<?php


declare( strict_types = 1 );


use JDWX\Log\AbstractLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


#[CoversClass( AbstractLogger::class )]
final class AbstractLoggerTest extends TestCase {


    public function testNormalizeLevel() : void {
        self::assertSame( LogLevel::EMERGENCY, AbstractLogger::normalizeLevel( 'emergency' ) );
        self::assertSame( LogLevel::ALERT, AbstractLogger::normalizeLevel( 'alert' ) );
        self::assertSame( LogLevel::CRITICAL, AbstractLogger::normalizeLevel( 'CRITICAL' ) );
        self::assertSame( LogLevel::ERROR, AbstractLogger::normalizeLevel( 'Error' ) );
        self::assertSame( LogLevel::WARNING, AbstractLogger::normalizeLevel( 'wArNing ' ) );
        self::assertSame( LogLevel::NOTICE, AbstractLogger::normalizeLevel( 'notICE' ) );
        self::assertSame( LogLevel::INFO, AbstractLogger::normalizeLevel( LOG_INFO ) );
        self::assertSame( LogLevel::DEBUG, AbstractLogger::normalizeLevel( LOG_DEBUG ) );
        self::assertNull( AbstractLogger::normalizeLevel( 'invalid' ) );
        self::assertSame( 'DEFAULT', AbstractLogger::normalizeLevel( 'invalid', 'DEFAULT' ) );
    }


    public function testNormalizeLevelEx() : void {
        self::assertSame( LogLevel::EMERGENCY, AbstractLogger::normalizeLevelEx( 'emergency' ) );
        $this->expectException( InvalidArgumentException::class );
        AbstractLogger::normalizeLevelEx( 12345 );
    }


}
