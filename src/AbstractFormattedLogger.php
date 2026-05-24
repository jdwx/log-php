<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Stringable;
use Throwable;


/**
 * This class can be used as a base for loggers that don't support
 * structured data, like StderrLogger. It focuses on generating a
 * reasonable string representation of the log message that can
 * be logged to a byte stream.
 */
abstract class AbstractFormattedLogger extends AbstractDirectLogger {


    /**
     * @param int|string           $level
     * @param Stringable|string    $message
     * @param array<string, mixed> $context
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function log( mixed $level, Stringable|string $message, array $context = [] ) : void {
        $stLevel = $this->renderLevel( $level, $context );
        $stMessage = $this->renderMessage( $message, $context );
        $stContext = $this->renderContext( $context );
        $this->write( trim( "{$stLevel}: {$stMessage} {$stContext}" ) );
    }


    /** @param mixed[] $context */
    public function renderContext( array $context ) : string {
        $context = $this->mergeGlobalContext( $context );
        if ( empty( $context ) ) {
            return '';
        }
        if ( isset( $context[ 'exception' ] ) && $context[ 'exception' ] instanceof Throwable ) {
            $context[ 'exception' ] = LogTools::exceptionToArray( $context[ 'exception' ] );
        }
        if ( isset( $context[ 'code' ] ) && 0 === $context[ 'code' ] ) {
            unset( $context[ 'code' ] );
        }
        return LogTools::formatArray( $context, $this->getDepth(), $this->getPropertyCount() );
    }


    /** @param array<int|string, mixed> $context */
    protected function renderLevel( mixed $level, array &$context ) : string {
        $stLevel = strtoupper( LogLevels::toStringEx( $level ) );
        if ( isset( $context[ 'class' ] ) ) {
            $stLevel .= '(' . $context[ 'class' ] . ')';
            unset( $context[ 'class' ] );
        }
        return $stLevel;
    }


    /** @param array<int|string, mixed> $context */
    protected function renderMessage( string|Stringable $message, array $context ) : string {
        return LogTools::interpolate( $message, $context );
    }


    abstract protected function write( string $stMessage ) : void;


}
