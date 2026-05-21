<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\VisitedCheck;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( VisitedCheck::class )]
final class VisitedCheckTest extends TestCase {


    public function testVisit() : void {
        $visit = new VisitedCheck();
        $x1 = new \stdClass();
        $x2 = new \stdClass();

        self::assertTrue( $visit->visit( $x1 ) );
        self::assertTrue( $visit->visit( $x2 ) );
        self::assertFalse( $visit->visit( $x1 ) );
        self::assertFalse( $visit->visit( $x2 ) );
    }


}
