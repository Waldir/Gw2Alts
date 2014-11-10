<?php
/*
 * class Bag extends Gw2Api 
 */
class Bag extends Gw2Api  {

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);
		
        $this->no_sell_or_sort = $APIItem['details']['no_sell_or_sort'];
        $this->size            = $APIItem['details']['size'];
	}
}
?>