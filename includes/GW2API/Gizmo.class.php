<?php
/*
 * class Gizmo extends Gw2Api 
 */
class Gizmo extends Gw2Api  {	

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);
		global $aGizmo_types;
		
        $this->sub_type = empty( $APIItem['details']['type'] ) ? null : $APIItem['details']['type'];
    }

   	public function getSubTypeInsert( ) {
		global $aGizmo_types;
		return (int) array_search( $this->sub_type, $aGizmo_types );
	}
}
?>