<?php


declare( strict_types = 1 );


namespace JDWX\Log;


/**
 * This is an interface for classes that nest loggers to help end users
 * dig down to the "real" logger, if there is one. This is helpful
 * when you're handed something that supports optional logging
 * functionality through a nested LoggerInterface and you want to discover
 * whether it has been initialized.
 *
 * In other words, this gives you a way to find out if the LoggerInterface
 * you're looking at actually plans to log things or if logged messages
 * are likely to silently disappear.
 */
interface HasLoggerInterface {


    public function getLogger() : ?\Psr\Log\LoggerInterface;


    public function hasLogger() : bool;


}
