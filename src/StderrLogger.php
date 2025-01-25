<?php


declare( strict_types = 1 );


namespace JDWX\Log;


class StderrLogger extends FormattedLogger {


    protected function write( string $stMessage ) : void {
        error_log( $stMessage );
    }


}
