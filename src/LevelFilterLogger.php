<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use Psr\Log\LoggerInterface;


class LevelFilterLogger extends FilterLogger {


    private int $level;


    /**
     * @param LoggerInterface $logger The wrapped logger
     * @param mixed $level The target level
     * @param bool $bExact True to match the level exactly, false to match levels equal to or more
     *                       severe than the specified level.
     */
    public function __construct( LoggerInterface $logger, mixed $level, private readonly bool $bExact = false ) {
        parent::__construct( $logger );
        $this->level = LogLevels::toIntEx( $level );
    }


    /**
     * @inheritDoc
     */
    protected function filter( int|string $level, \Stringable|string $message, array $context ) : bool {
        if ( $this->bExact ) {
            return LogLevels::toIntEx( $level ) === $this->level;
        }
        return LogLevels::compare( $level, $this->level ) <= 0;
    }


}
