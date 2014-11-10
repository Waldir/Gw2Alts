<?php
/*
 * class Container extends Gw2Api 
 */
class Container extends Gw2Api  {	

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);	
		
        $this->sub_type = empty( $APIItem['details']['type'] ) ? null : $APIItem['details']['type'];
    }

    // genetic tolltip


    public function getSubTypeInsert( ) {
		global $aContainer_types;
		return (int) array_search( $this->sub_type, $aContainer_types );
	}
}
?>