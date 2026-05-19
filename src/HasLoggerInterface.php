<?php


declare( strict_types = 1 );


namespace JDWX\Log;


interface HasLoggerInterface {


    public function getLogger() : ?LoggerInterface;


}
