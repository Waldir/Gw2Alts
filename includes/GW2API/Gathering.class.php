<?php
/*
 * class Gathering   extends Gw2Api 
 */
class Gathering extends Gw2Api  {	

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);
		
        $this->sub_type = empty( $APIItem['details']['type'] ) ? null : $APIItem['details']['type'];
    }

    public function getSubTypeInsert( ) {
		global $aGathering_types;
		return (int) array_search( $this->sub_type, $aGathering_types );
	}
}
?>