<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;


#[CoversClass( AbstractLogger::class )]
final class AbstractLoggerTest extends TestCase {


    /**
     * @noinspection PhpDeprecationInspection
     * @suppress PhanDeprecatedFunction
     */
    public function testCompareLevels() : void {
        self::assertSame( 0, AbstractLogger::compareLevels( 'emergency', LOG_EMERG ) );
        self::assertSame( 1, AbstractLogger::compareLevels( 'alert', 'emergency' ) );
        self::assertSame( -1, AbstractLogger::compareLevels( 'CRITICAL', 'Error' ) );
        self::assertSame( 0, AbstractLogger::compareLevels( LOG_WARNING, 'wArNing ' ) );
        self::assertSame( -1, AbstractLogger::compareLevels( 'notICE', LOG_INFO ) );
        $this->expectException( InvalidArgumentException::class );
        self::assertSame( 1, AbstractLogger::compareLevels( LOG_DEBUG, 'invalid' ) );
    }


    public function testGetLogger() : void {
        $log = new class extends AbstractLogger {


            public function log( $level, \Stringable|string $message, array $context = [] ) : void {
                // TODO: Implement log() method.
            }


        };
        self::assertSame( $log, $log->getLogger() );
    }


    /**
     * @noinspection PhpDeprecationInspection
     * @suppress PhanDeprecatedFunction
     */
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


    /**
     * @noinspection PhpDeprecationInspection
     * @suppress PhanDeprecatedFunction
     */
    public function testNormalizeLevelEx() : void {
        self::assertSame( LogLevel::EMERGENCY, AbstractLogger::normalizeLevelEx( 'emergency' ) );
        $this->expectException( InvalidArgumentException::class );
        AbstractLogger::normalizeLevelEx( 12345 );
    }


    /**
     * @noinspection PhpDeprecationInspection
     * @suppress PhanDeprecatedFunction
     */
    public function testNormalizeLevelInt() : void {
        self::assertSame( 0, AbstractLogger::normalizeLevelInt( 'emergency' ) );
        self::assertSame( 1, AbstractLogger::normalizeLevelInt( 'alert' ) );
        self::assertSame( 2, AbstractLogger::normalizeLevelInt( 'CRITICAL' ) );
        self::assertSame( 3, AbstractLogger::normalizeLevelInt( 'Error' ) );
        self::assertSame( 4, AbstractLogger::normalizeLevelInt( 'wArNing ' ) );
        self::assertSame( 5, AbstractLogger::normalizeLevelInt( 'notICE' ) );
        self::assertSame( 6, AbstractLogger::normalizeLevelInt( LOG_INFO ) );
        self::assertSame( 7, AbstractLogger::normalizeLevelInt( LOG_DEBUG ) );
        self::assertNull( AbstractLogger::normalizeLevelInt( 'invalid' ) );
        self::assertSame( 42, AbstractLogger::normalizeLevelInt( 'invalid', 42 ) );
        self::assertSame( 12345, AbstractLogger::normalizeLevelInt( 12345 ) );
        $this->expectException( InvalidArgumentException::class );
        AbstractLogger::normalizeLevelInt( 3.14 );
    }


    /**
     * @noinspection PhpDeprecationInspection
     * @suppress PhanDeprecatedFunction
     */
    public function testNormalizeLevelIntEx() : void {
        self::assertSame( 0, AbstractLogger::normalizeLevelIntEx( 'emergency' ) );
        self::assertSame( 7, AbstractLogger::normalizeLevelIntEx( LOG_DEBUG ) );
        self::assertSame( 12345, AbstractLogger::normalizeLevelIntEx( 12345 ) );
    }


    /**
     * @noinspection PhpDeprecationInspection
     * @suppress PhanDeprecatedFunction
     */
    public function testNormalizeLevelIntExForBadValue() : void {
        $this->expectException( InvalidArgumentException::class );
        AbstractLogger::normalizeLevelIntEx( 'invalid' );
    }


    /**
     * @noinspection PhpDeprecationInspection
     * @suppress PhanDeprecatedFunction
     */
    public function testNormalizeLevelIntExForWrongType() : void {
        $this->expectException( InvalidArgumentException::class );
        AbstractLogger::normalizeLevelIntEx( 12.34 );
    }


}
