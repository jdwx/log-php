<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Stringable;


abstract class AbstractContextDecoratorLogger extends ProxyLogger {


    public function log( $level, Stringable|string $message, array $context = [] ) : void {
        parent::log( $level, $message, $this->decorateContext( $context ) );
    }


    /**
     * @param array<string, mixed> $i_rContext
     * @return array<string, mixed>
     */
    abstract protected function decorateContext( array $i_rContext ) : array;


}
