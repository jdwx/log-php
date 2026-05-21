<?php


declare( strict_types = 1 );


namespace JDWX\Log;


class VisitedCheck {


    /** @var array<string, true> */
    private array $rVisited = [];


    public function visit( object $i_visited ) : bool {
        $stVisited = spl_object_hash( $i_visited );
        if ( isset( $this->rVisited[ $stVisited ] ) ) {
            return false;
        }
        $this->rVisited[ $stVisited ] = true;
        return true;
    }


}
