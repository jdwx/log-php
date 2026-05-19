<?php


declare( strict_types = 1 );


namespace JDWX\Log;


class BufferBatchLogger extends AbstractBatchLogger {


    /** @var list<LogEntry> */
    public array $rLastBatch = [];

    public string $stLastLevel;

    /** @var array<int|string, mixed> */
    public array $rCommonContext = [];


    /**
     * @inheritDoc
     */
    protected function batch( string $i_stLevel, array $i_rLogMessages, array $i_rCommonContext ) : void {
        $this->rLastBatch = $i_rLogMessages;
        $this->stLastLevel = $i_stLevel;
        $this->rCommonContext = $i_rCommonContext;
    }


}

