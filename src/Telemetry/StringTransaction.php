<?php


declare( strict_types = 1 );


namespace JDWX\Log\Telemetry;


use JDWX\Json\Json;
use Stringable;


class StringTransaction extends AbstractTransaction implements Stringable {


    private string $string = '';

    private bool $bFinished = false;


    public function __toString() : string {
        if ( ! $this->bFinished ) {
            $this->finish();
        }
        return $this->string;
    }


    protected function commit() : void {
        $this->bFinished = true;
        $r = $this->contextSerialize();
        $this->string = Json::encodePretty( $r );
    }


}
