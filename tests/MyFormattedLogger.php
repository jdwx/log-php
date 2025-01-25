<?php


declare( strict_types = 1 );


class MyFormattedLogger extends JDWX\Log\FormattedLogger {


    public string $stWritten = '';


    protected function write( string $stMessage ) : void {
        $this->stWritten .= $stMessage;
    }


}
