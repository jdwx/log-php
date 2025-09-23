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
        $this->level = static::normalizeLevelIntEx( $level );
    }


    /**
     * @inheritDoc
     */
    protected function filter( int|string $level, \Stringable|string $message, array $context ) : bool {
        if ( $this->bExact ) {
            return static::normalizeLevelIntEx( $level ) === $this->level;
        }
        return static::compareLevels( $level, $this->level ) <= 0;
    }


}
