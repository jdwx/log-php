<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


use JDWX\Json\Json;
use Stringable;


class StringTransaction extends ParentNode implements TransactionInterface, Stringable {


    private string $string;

    private bool $bFinished = false;


    public function __toString() : string {
        if ( ! $this->bFinished ) {
            $this->finish();
        }
        return $this->string;
    }


    public function finish() : void {
        $this->bFinished = true;
        $r = $this->contextSerialize();
        if ( empty( $r ) ) {
            $this->string = '';
            return;
        }
        $this->string = Json::encodePretty( $r );
    }


}
