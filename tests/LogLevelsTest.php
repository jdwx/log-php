<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\LogLevels;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


class LogLevelsTest extends TestCase {


    public function testCompare() : void {
        self::assertSame( 0, LogLevels::compare( LogLevel::INFO, LogLevel::INFO ) );
        self::assertGreaterThan( 0, LogLevels::compare( LogLevel::DEBUG, LogLevel::INFO ) );
        self::assertLessThan( 0, LogLevels::compare( LogLevel::ERROR, LogLevel::WARNING ) );
    }


    public function testIsValid() : void {
        self::assertTrue( LogLevels::isValid( LogLevel::DEBUG ) );
        self::assertTrue( LogLevels::isValid( LOG_DEBUG ) );
        self::assertFalse( LogLevels::isValid( 'unknown' ) );
        self::assertFalse( LogLevels::isValid( 9999 ) );
    }


    public function testMax() : void {
        self::assertSame( LogLevel::DEBUG, LogLevels::leastSevere( LogLevel::DEBUG, LogLevel::INFO ) );
        self::assertSame( LOG_WARNING, LogLevels::leastSevere( LogLevel::ERROR, LOG_WARNING ) );
        self::assertSame( LOG_NOTICE, LogLevels::leastSevere( LOG_NOTICE, LogLevel::NOTICE ) );
        self::assertSame( LogLevel::INFO, LogLevels::leastSevere( LOG_ERR, LOG_WARNING, LogLevel::INFO ) );
        self::assertSame( LogLevel::INFO, LogLevels::leastSevere( LOG_WARNING, LogLevel::INFO, LOG_ERR ) );
        self::assertSame( LogLevel::INFO, LogLevels::leastSevere( LogLevel::INFO, LOG_ERR, LOG_WARNING ) );
        $this->expectException( \InvalidArgumentException::class );
        LogLevels::leastSevere();
    }


    public function testMin() : void {
        self::assertSame( LogLevel::INFO, LogLevels::mostSevere( LogLevel::DEBUG, LogLevel::INFO ) );
        self::assertSame( LogLevel::ERROR, LogLevels::mostSevere( LogLevel::ERROR, LOG_WARNING ) );
        self::assertSame( LOG_NOTICE, LogLevels::mostSevere( LOG_NOTICE, LogLevel::NOTICE ) );
        self::assertSame( LOG_ERR, LogLevels::mostSevere( LOG_ERR, LOG_WARNING, LogLevel::INFO ) );
        self::assertSame( LOG_ERR, LogLevels::mostSevere( LOG_WARNING, LogLevel::INFO, LOG_ERR ) );
        self::assertSame( LOG_ERR, LogLevels::mostSevere( LogLevel::INFO, LOG_ERR, LOG_WARNING ) );
        $this->expectException( \InvalidArgumentException::class );
        LogLevels::mostSevere();
    }


    public function testToInt() : void {
        self::assertSame( LOG_INFO, LogLevels::toInt( LogLevel::INFO ) );
        self::assertSame( LOG_ERR, LogLevels::toInt( LogLevel::ERROR ) );
        self::assertSame( LOG_DEBUG, LogLevels::toInt( 'debug' ) );
        self::assertSame( LOG_DEBUG, LogLevels::toInt( 'Debug' ) );
        self::assertSame( LOG_DEBUG, LogLevels::toInt( 'DEBUG' ) );
        self::assertNull( LogLevels::toInt( 'unknown' ) );
    }


    public function testToIntEx() : void {
        self::assertSame( LOG_INFO, LogLevels::toIntEx( LogLevel::INFO ) );
        self::assertSame( LOG_ERR, LogLevels::toIntEx( LogLevel::ERROR ) );
        self::assertSame( LOG_DEBUG, LogLevels::toIntEx( 'debug' ) );
        $this->expectException( \InvalidArgumentException::class );
        LogLevels::toIntEx( 'unknown' );
    }


    public function testToString() : void {
        self::assertSame( LogLevel::INFO, LogLevels::toString( LOG_INFO ) );
        self::assertSame( LogLevel::ERROR, LogLevels::toString( LOG_ERR ) );
        self::assertSame( LogLevel::DEBUG, LogLevels::toString( 'debug' ) );
        self::assertSame( LogLevel::DEBUG, LogLevels::toString( 'Debug' ) );
        self::assertSame( LogLevel::DEBUG, LogLevels::toString( 'DEBUG' ) );
        self::assertNull( LogLevels::toString( 9999 ) );
    }


    public function testToStringEx() : void {
        self::assertSame( LogLevel::INFO, LogLevels::toStringEx( LOG_INFO ) );
        self::assertSame( LogLevel::ERROR, LogLevels::toStringEx( LOG_ERR ) );
        $this->expectException( \InvalidArgumentException::class );
        LogLevels::toStringEx( 9999 );
    }


}
