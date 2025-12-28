<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


abstract class AbstractTransaction extends ParentNode implements TransactionInterface {


    public function finish() : void {
        parent::finish();
        $this->commit();
    }


    abstract protected function commit() : void;


}
