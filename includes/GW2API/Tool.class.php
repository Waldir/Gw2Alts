<?php
/*
 * class Tool  extends Gw2Api 
 */
class Tool extends Gw2Api  {

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);
		
        $this->charges  = $APIItem['details']['charges'];
        $this->sub_type = empty( $APIItem['details']['type'] ) ? null : $APIItem['details']['type'];
    }
	
	public function getDescription(){
		return $this->description . '<br /> Charges: ' . $this->charges;
	}

	public function getSubTypeInsert( ) {
		global $aTool_types;
		return (int) array_search( $this->sub_type, $aTool_types );
	}
}
?>