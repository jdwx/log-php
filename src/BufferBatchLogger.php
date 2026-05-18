<?php


declare( strict_types = 1 );


namespace JDWX\Log;


class BufferBatchLogger extends AbstractBatchLogger {


    /** @var list<LogEntry> */
    public array $rLastBatch = [];

    public string $stLastLevel;


    /**
     * @inheritDoc
     */
    protected function batch( string $i_stLevel, array $i_rLogMessages ) : void {
        $this->rLastBatch = $i_rLogMessages;
        $this->stLastLevel = $i_stLevel;
    }


}

