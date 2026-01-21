<?php


declare( strict_types = 1 );


namespace JDWX\Log;


interface LogEntryInterface extends LevelInterface {


    /** @return mixed[] */
    public function context() : array;


    public function message() : string;


}