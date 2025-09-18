<?php


declare( strict_types = 1 );


namespace JDWX\Log;


global $rExampleErrorLog;

$rExampleErrorLog = [];

/** @noinspection PhpFunctionNamingConventionInspection */
function error_log( string $i_st ) : void {
    global $rExampleErrorLog;
    $rExampleErrorLog[] = $i_st;
}


function FetchErrorLine( int $i_n ) : ?string {
    global $rExampleErrorLog;
    return $rExampleErrorLog[ $i_n ] ?? null;
}
