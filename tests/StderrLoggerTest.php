<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\StderrLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use function JDWX\Log\FetchErrorLine;


require __DIR__ . '/ErrorLogInjection.php';


#[CoversClass( StderrLogger::class )]
final class StderrLoggerTest extends TestCase {


    public function testLog() : void {
        $logger = new StderrLogger();
        $logger->info( 'Test {x}', [ 'x' => 123 ] );

        self::assertSame( 'INFO: Test 123 {', FetchErrorLine( 0 ) );
        self::assertSame( '  x: 123', FetchErrorLine( 1 ) );
        self::assertSame( '}', FetchErrorLine( 2 ) );
        self::assertNull( FetchErrorLine( 3 ) );

        $this->expectException( InvalidArgumentException::class );
        $logger->log( 'invalid', 'Nope.' );
    }


}
