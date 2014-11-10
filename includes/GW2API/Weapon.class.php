<?php
/*
 * class Weapon extends Gw2Api 
 */
class Weapon extends Gw2Api  {

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);
		
        $this->damage_type      = $APIItem['details']['damage_type'];
        $this->infusion_slots   = $APIItem['details']['infusion_slots'];
        $this->suffix_item_id   = empty( $APIItem['details']['suffix_item_id'] ) ? null : (int)$APIItem['details']['suffix_item_id'];
        $this->min_power        = (int) $APIItem['details']['min_power'];
        $this->max_power        = (int) $APIItem['details']['max_power'];
        $this->defense          = (int) $APIItem['details']['defense'];
		$this->default_skin     = (int) $APIItem['default_skin'];
        $this->suffix_item_id   = empty( $APIItem['details']['suffix_item_id'] ) ? null : (int)$APIItem['details']['suffix_item_id'];
        $this->sub_type         = empty( $APIItem['details']['type'] ) ? null : $APIItem['details']['type'];
        $this->infix_upgrade    = empty( $APIItem['details']['infix_upgrade'] ) ? array() : $APIItem['details']['infix_upgrade'];
        $this->attributes_array = $this->getAttributesArray();
	}

    public function getTooltip() {
        return <<<HTML
        <div>
            <img src="{$this->img_file}" class="item-icon-tooltip">
            <span class="{$this->rarity} item_title">{$this->getName()}</span>
        </div>
        <div>Weapon Strength: <span class="Masterwork">{$this->getMinPower()} - {$this->getMaxPower()}</span> <span class="Junk">({$this->damage_type})</span></div>
        {$this->getDefenseDisplay()}
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
        global $aWeapon_types;
        return (int) array_search( $this->sub_type, $aWeapon_types );
    }

    public function getDefense( ){ 
		return $this->defense;
	}	
    public function getMinPower( ) {
        return $this->min_power;
    }
    
    public function getMaxPower( ) {
        return $this->max_power;
    }
    
    public function getDamageType( ) {
		global $aWeapon_dmg_types;
		return (int) array_search( $this->damage_type, $aWeapon_dmg_types );
    }
	
}
?>