<?php


declare( strict_types = 1 );


namespace JDWX\Log;


interface ContextSerializable {


    /** @return array<int|string, mixed[]|bool|float|int|string|null>|bool|float|int|string|null */
    public function contextSerialize() : array|bool|float|int|string|null;


}