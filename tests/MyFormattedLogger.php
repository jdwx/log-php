<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\FormattedLogger;


class MyFormattedLogger extends FormattedLogger {


    public string $stWritten = '';


    protected function write( string $stMessage ) : void {
        $this->stWritten .= $stMessage;
    }


}
