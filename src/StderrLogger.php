<?php


declare( strict_types = 1 );


namespace JDWX\Log;


class StderrLogger extends FormattedLogger {


    /** @codeCoverageIgnore */
    protected function write( string $stMessage ) : void {
        error_log( $stMessage );
    }


}
