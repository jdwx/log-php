<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerTrait;


/**
 * @deprecated Use Psr\Log\LoggerTrait
 * @phpstan-ignore trait.unused
 *
 * Retain until the next minor version release after 2025-09-26.
 */
trait RelayLoggerTrait {


    use LoggerTrait;
}
