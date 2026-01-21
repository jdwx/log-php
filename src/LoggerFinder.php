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


    public function __construct( private ?LoggerInterface $logger = null,
                                 private readonly string  $stRegistryId = LoggerRegistry::DEFAULT_LOGGER_ID ) {}


    /**
     * @param mixed ...$rOptions All options to try to find a logger, in order of preference.
     * @return LoggerInterface|null
     */
    public static function find( mixed ...$rOptions ) : ?LoggerInterface {
        return ( new self() )->try( ...$rOptions )->getLogger();
    }


    /**
     * @param string $i_stClass The class that insists on having a logger available.
     * @param mixed ...$rOptions All options to try to find a logger, in order of preference.
     * @return LoggerInterface
     */
    public static function findEx( string $i_stClass, mixed ...$rOptions ) : LoggerInterface {
        return ( new self() )->try( ...$rOptions )->getLoggerEx( $i_stClass );
    }


    public function getLogger() : ?LoggerInterface {
        $this->lastDitchEffort();
        return $this->logger;
    }


    /**
     * @param string $i_stClass The class that insists on having a logger available.
     * @return LoggerInterface
     */
    public function getLoggerEx( string $i_stClass ) : LoggerInterface {
        $logger = $this->getLogger();
        if ( $logger instanceof LoggerInterface ) {
            return $logger;
        }
        throw new \RuntimeException( "A logger is required by {$i_stClass}, but none could be found." );
    }


    public function try( mixed ...$rOptions ) : static {
        foreach ( $rOptions as $x ) {
            $this->logger ??= $this->unwind( $x );
        }
        return $this;
    }


    protected function lastDitchEffort() : void {
        if ( $this->logger instanceof LoggerInterface ) {
            return;
        }
        $this->logger = LoggerRegistry::get( $this->stRegistryId );
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
