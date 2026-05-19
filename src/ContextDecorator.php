<?php


declare( strict_types = 1 );


namespace JDWX\Log;


class ContextDecorator extends AbstractContextDecorator {


    /** @var array<int|string, mixed> */
    private array $rExtraContext = [];


    public function setContext( int|string $i_key, mixed $i_value ) : void {
        $this->rExtraContext[ $i_key ] = $i_value;
    }


    public function unsetContext( int|string $i_key ) : void {
        unset( $this->rExtraContext[ $i_key ] );
    }


    /**
     * @param array<int|string, mixed> $i_rContext
     * @return array<int|string, mixed>
     */
    protected function decorateContext( array $i_rContext ) : array {
        return array_merge( $this->rExtraContext, $i_rContext );
    }


}
