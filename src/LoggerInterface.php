<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Stringable;
use Throwable;


interface LoggerInterface extends \Psr\Log\LoggerInterface {


    /** @param array<int|string, mixed> $i_rContext */
    public function alertFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                 array     $i_rContext = [] ) : void;


    /** @param array<int|string, mixed> $i_rContext */
    public function criticalFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                    array     $i_rContext = [] ) : void;


    /** @param array<int|string, mixed> $i_rContext */
    public function debugFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                 array     $i_rContext = [] ) : void;


    /** @param array<int|string, mixed> $i_rContext */
    public function emergencyFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                     array     $i_rContext = [] ) : void;


    /** @param array<int|string, mixed> $i_rContext */
    public function errorFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                 array     $i_rContext = [] ) : void;


    public function flushLog() : void;


    /** @param array<int|string, mixed> $i_rContext */
    public function infoFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                array     $i_rContext = [] ) : void;


    /** @param array<int|string, mixed> $i_rContext */
    public function noticeFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                  array     $i_rContext = [] ) : void;


    /** @param array<int|string, mixed> $i_rContext */
    public function warningFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                   array     $i_rContext = [] ) : void;


}