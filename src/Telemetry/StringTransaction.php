<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


use JDWX\Json\Json;
use Stringable;


class StringTransaction extends AbstractTransaction implements Stringable {


    private string $string = '';


    public function __toString() : string {
        if ( ! $this->isFinished() ) {
            $this->finish();
        }
        return $this->string;
    }


    protected function commit() : void {
        $r = $this->contextSerialize();
        $this->string = Json::encodePretty( $r );
    }


}
