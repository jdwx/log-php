<?php


declare( strict_types = 1 );


namespace JDWX\Log;


abstract class AbstractDirectLogger implements HasLoggerInterface, LoggerInterface {


    use LoggerTrait;


    public function __construct( private readonly ?GlobalContext $gtx = null ) {}


    public function getGlobalContext() : ?GlobalContext {
        return $this->gtx;
    }


    public function getLogger() : ?\Psr\Log\LoggerInterface {
        return $this;
    }


    public function hasLogger() : bool {
        return $this->getLogger() instanceof \Psr\Log\LoggerInterface;
    }


    /**
     * @param array<int|string, mixed> $i_rContext
     * @return array<int|string, mixed>
     */
    protected function mergeGlobalContext( array $i_rContext ) : array {
        return array_merge( $this->getGlobalContext()?->jsonSerialize() ?? [], $i_rContext );
    }


}
