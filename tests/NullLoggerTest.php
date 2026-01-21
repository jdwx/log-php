<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\NullLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;


class NullLoggerTest extends TestCase {


    public function testLog() : void {
        $null = new NullLogger();
        # It doesn't do anything, so about all we can test is whether it explodes if
        # you give it an invalid level
        $this->expectException( InvalidArgumentException::class );
        $null->log( 'invalid', 'message' );
    }


}
