<?php


declare( strict_types = 1 );


namespace JDWX\Log;


class ContextDecoratorLogger extends AbstractContextDecoratorLogger {


    /** @param array<int|string, mixed> $rExtraContext */
    public function __construct( \Psr\Log\LoggerInterface $i_logger, private array $rExtraContext = [] ) {
        parent::__construct( $i_logger );
    }


    public function getContext( int|string $i_key, int|string|null $i_subKey = null ) : mixed {
        if ( ! is_null( $i_subKey ) ) {
            return $this->rExtraContext[ $i_key ][ $i_subKey ] ?? null;
        }
        return $this->rExtraContext[ $i_key ] ?? null;
    }


    public function hasContext( int|string $i_key, int|string|null $i_subKey = null ) : bool {
        if ( ! array_key_exists( $i_key, $this->rExtraContext ) ) {
            return false;
        }
        if ( is_null( $i_subKey ) ) {
            return true;
        }
        if ( ! is_array( $this->rExtraContext[ $i_key ] ) ) {
            return false;
        }
        return array_key_exists( $i_subKey, $this->rExtraContext[ $i_key ] );
    }


    public function mergeContext( int|string $i_key, string $i_subKey, mixed $i_context ) : void {
        if ( ! array_key_exists( $i_key, $this->rExtraContext ) ) {
            $this->setContext( $i_key, [ $i_subKey => $i_context ] );
            return;
        }
        if ( ! is_array( $this->rExtraContext[ $i_key ] ) ) {
            throw new \InvalidArgumentException( "Merging key {$i_key}:{$i_subKey} but {$i_key} is not an array." );
        }
        $this->rExtraContext[ $i_key ][ $i_subKey ] = $i_context;
    }


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
    protected function decorateContext( int|string $i_level, array $i_rContext ) : array {
        return array_merge( $this->rExtraContext, $i_rContext );
    }


}
