<?php


declare( strict_types = 1 );


namespace JDWX\Log\Tests;


use JDWX\Log\AbstractFormattedLogger;


class MyFormattedLogger extends AbstractFormattedLogger {


    public string $stWritten = '';


    protected function write( string $stMessage ) : void {
        $this->stWritten .= $stMessage;
    }


}
