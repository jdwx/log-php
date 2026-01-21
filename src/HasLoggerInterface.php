<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface;


interface HasLoggerInterface {


    public function getLogger() : ?LoggerInterface;


}
