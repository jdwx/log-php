<?php


declare( strict_types = 1 );


namespace JDWX\Log;


class StderrLogger extends FormattedLogger {


    /** @codeCoverageIgnore */
    protected function write( string $stMessage ) : void {
        foreach ( explode( "\n", $stMessage ) as $stLine ) {
            /** @noinspection ForgottenDebugOutputInspection */
            error_log( $stLine );
        }
    }


}
