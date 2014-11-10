<?php
/*
 * class Trinket extends Gw2Api 
 */
class Trinket extends Gw2Api  {	

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);
		
		$this->sub_type 				= empty( $APIItem['details']['type'] ) ? null : $APIItem['details']['type'];
		$this->suffix_item_id           = empty( $APIItem['details']['suffix_item_id'] )           ? null : (int) $APIItem['details']['suffix_item_id'];
		$this->secondary_suffix_item_id = empty( $APIItem['details']['secondary_suffix_item_id'] ) ? null : (int) $APIItem['details']['secondary_suffix_item_id'];
		$this->infix_upgrade            = empty( $APIItem['details']['infix_upgrade'] )            ? array(): $APIItem['details']['infix_upgrade'];
		$this->infusion_slots           = $APIItem['details']['infusion_slots'];
		$this->attributes_array         = $this->getAttributesArray();
	}

	public function getTooltip() {
		return <<<HTML
		<div>
			<img src="{$this->img_file}" class="item-icon-tooltip">
			<span class="{$this->rarity} item_title">{$this->getName()}</span>
		</div>
		{$this->getAttributesDisplay()}
		{$this->getSuffixDisplay()}
		{$this->getInfusionSlotsDisplay()}
		<div>{$this->rarity}</div>
		<div>{$this->getSubType()}</div> 
		{$this->getLevelDisplay()}
		{$this->getDescriptionDisplay()}
		{$this->getSoulboundStatus()}
		{$this->getVendorValueDisplay()}
HTML;
	}

	public function getSubTypeInsert( ) {
		global $aTrinket_types;
		return (int) array_search( $this->sub_type, $aTrinket_types );
	}
}
?>