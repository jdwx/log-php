<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface;


/**
 * This class helps find the best available logger when some classes may
 * implement LoggerInterface but passthrough to an optional logger and
 * might thereby potentially discard logged messages.
 *
 * (Such classes should implement HasLoggerInterface so this finder
 * can discern when their inner logger is null and keep looking.)
 *
 * This will keep the first LoggerInterface it finds that:
 * - Isn't null
 * - Implements LoggerInterface
 * - Does not implement HasLoggerInterface or does but returns itself
 *
 * This is objectively inferior to proper service discovery, but
 * is better than nothing when explicit dependency on a certain service
 * discovery infrastructure is untenable.
 */
class LoggerFinder implements HasLoggerInterface {


    public function __construct( private ?LoggerInterface $logger = null ) {}


    public function getLogger() : ?LoggerInterface {
        return $this->logger;
    }


    public function try( mixed $x ) : void {
        $this->logger ??= $this->unwind( $x );
    }


    private function unwind( mixed $i_logger ) : ?LoggerInterface {
        if ( $i_logger instanceof HasLoggerInterface ) {
            $logger = $i_logger->getLogger();
            # $i_logger is not null, and $i_logger->getLogger() returns ?LoggerInterface,
            # so if it returned itself, it necessarily implements LoggerInterface,
            # and we don't need to recheck that.
            if ( $logger === $i_logger ) {
                return $logger;
            }
            return $this->unwind( $logger );
        }
        return ( $i_logger instanceof LoggerInterface ) ? $i_logger : null;
    }


}
