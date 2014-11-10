<?php
/*
 * class Armor extends Gw2Api 
 */
class Armor extends Gw2Api{

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);	
		
		$this->weight_class             = $APIItem['details']['weight_class'];		
		$this->defense 					= (int) $APIItem['details']['defense'];
		$this->default_skin 			= (int) $APIItem['default_skin'];
		$this->sub_type 				= empty( $APIItem['details']['type'] ) ? null : $APIItem['details']['type'];
		$this->suffix_item_id           = empty( $APIItem['details']['suffix_item_id'] )           ? null : (int) $APIItem['details']['suffix_item_id'];
		$this->secondary_suffix_item_id = empty( $APIItem['details']['secondary_suffix_item_id'] ) ? null : (int) $APIItem['details']['secondary_suffix_item_id'];
		$this->infix_upgrade            = empty( $APIItem['details']['infix_upgrade'] )            ? array(): $APIItem['details']['infix_upgrade'];
		$this->infusion_slots           = $APIItem['details']['infusion_slots'];
		$this->attributes_array         = $this->getAttributesArray();

	}
	
	// Getters
    public function getDefense( ){ 
		return $this->defense;
	}
	
    public function getWeightClass( ){ 
		global $aArmor_weights; 
		return array_search( $this->weight_class, $aArmor_weights );
	}
	
	public function getSubTypeInsert( ) {
		global $aArmor_types;
		return (int) array_search( $this->sub_type, $aArmor_types );
	}
	
	public function getTooltip() {

		return <<<HTML
		<div class="{$this->rarity} item_title">
			<img src="{$this->img_file}" class="item-icon-tooltip">
			{$this->getName()}
		</div>
		<div>Defense: <span class="Masterwork">{$this->getDefense()}</span></div>
        {$this->getAttributesDisplay()}
        {$this->getSuffixDisplay()}
        {$this->getInfusionSlotsDisplay()}
		<div>{$this->getInfusionSlotsDisplay( )}</div>
		<div>{$this->rarity}</div>
		<div>{$this->weight_class}</div>
		<div>{$this->getSubType()} Armor</div> 
        {$this->getLevelDisplay()}
        {$this->getDescriptionDisplay()}
        {$this->getSoulboundStatus()}
        {$this->getVendorValueDisplay()}
HTML;
	}
}
?>