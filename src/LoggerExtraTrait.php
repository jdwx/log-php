<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LogLevel;
use Stringable;
use Throwable;


trait LoggerExtraTrait {


    public function alertFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                 array     $i_rContext = [] ) : void {
        $stMessage = $i_nstMessage ?? $i_ex->getMessage();
        $i_rContext[ 'exception' ] = LogTools::exceptionToArray( $i_ex );
        $this->log( LogLevel::ALERT, $stMessage, $i_rContext );
    }


    /** @param array<int|string, mixed> $i_rContext */
    public function criticalFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                    array     $i_rContext = [] ) : void {
        $stMessage = $i_nstMessage ?? $i_ex->getMessage();
        $i_rContext[ 'exception' ] = LogTools::exceptionToArray( $i_ex );
        $this->log( LogLevel::CRITICAL, $stMessage, $i_rContext );
    }


    public function debugFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                 array     $i_rContext = [] ) : void {
        $stMessage = $i_nstMessage ?? $i_ex->getMessage();
        $i_rContext[ 'exception' ] = LogTools::exceptionToArray( $i_ex );
        $this->log( LogLevel::DEBUG, $stMessage, $i_rContext );
    }


    public function emergencyFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                     array     $i_rContext = [] ) : void {
        $stMessage = $i_nstMessage ?? $i_ex->getMessage();
        $i_rContext[ 'exception' ] = LogTools::exceptionToArray( $i_ex );
        $this->log( LogLevel::EMERGENCY, $stMessage, $i_rContext );
    }


    /** @param array<int|string, mixed> $i_rContext */
    public function errorFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                 array     $i_rContext = [] ) : void {
        $stMessage = $i_nstMessage ?? $i_ex->getMessage();
        $i_rContext[ 'exception' ] = LogTools::exceptionToArray( $i_ex );
        $this->log( LogLevel::ERROR, $stMessage, $i_rContext );
    }


    /** @codeCoverageIgnore */
    public function flushLog() : void {}


    /** @param array<int|string, mixed> $i_rContext */
    public function infoFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                array     $i_rContext = [] ) : void {
        $stMessage = $i_nstMessage ?? $i_ex->getMessage();
        $i_rContext[ 'exception' ] = LogTools::exceptionToArray( $i_ex );
        $this->log( LogLevel::INFO, $stMessage, $i_rContext );
    }


    abstract public function log( $level, string|Stringable $message, array $context = [] ) : void;


    /** @param array<int|string, mixed> $i_rContext */
    public function noticeFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                  array     $i_rContext = [] ) : void {
        $stMessage = $i_nstMessage ?? $i_ex->getMessage();
        $i_rContext[ 'exception' ] = LogTools::exceptionToArray( $i_ex );
        $this->log( LogLevel::NOTICE, $stMessage, $i_rContext );
    }


    /** @param array<int|string, mixed> $i_rContext */
    public function warningFromEx( Throwable $i_ex, string|Stringable|null $i_nstMessage = null,
                                   array     $i_rContext = [] ) : void {
        $stMessage = $i_nstMessage ?? $i_ex->getMessage();
        $i_rContext[ 'exception' ] = LogTools::exceptionToArray( $i_ex );
        $this->log( LogLevel::WARNING, $stMessage, $i_rContext );
    }


}