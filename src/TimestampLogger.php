<?php


declare( strict_types = 1 );


namespace JDWX\Log;


use DateTime;
use DateTimeZone;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;


/**
 * Adds a timestamp to each log message. This is most useful in conjunction with
 * StderrLogger, which just dumps the log to stderr without decoration.
 */
class TimestampLogger extends AbstractLogger {


    private readonly ?DateTimeZone $timezone;


    /**
     * @param LoggerInterface $parent The logger to wrap.
     * @param string $format The date format string (see DateTime::format()).
     * @param DateTimeZone|string|null $timezone The timezone for timestamps.
     *        Defaults to UTC. Pass null to use the local timezone.
     */
    public function __construct(
        private readonly LoggerInterface $parent,
        private readonly string          $format = '[Y-m-d H:i:s] ',
        DateTimeZone|string|null         $timezone = 'UTC',
    ) {
        if ( is_string( $timezone ) ) {
            $this->timezone = new DateTimeZone( $timezone );
        } else {
            $this->timezone = $timezone;
        }
    }


    /**
     * @param int|string $level
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function log( $level, \Stringable|string $message, array $context = [] ) : void {
        $dt = new DateTime( 'now', $this->timezone );
        $message = $dt->format( $this->format ) . $message;
        $this->parent->log( $level, $message, $context );
    }


}
