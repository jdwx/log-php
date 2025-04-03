<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerTrait;


/**
 * @deprecated Use Psr\Log\LoggerTrait
 *
 * Retain until the next minor version release after 2025-09-26.
 * @phpstan-ignore trait.unused
 */
trait RelayLoggerTrait {


    use LoggerTrait;
}
