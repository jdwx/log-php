<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


interface TransactionInterface extends ParentNodeInterface {


    public function finish() : void;


}