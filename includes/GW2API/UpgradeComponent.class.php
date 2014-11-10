<?php
/*
 * class UpgradeComponent extends Gw2Api 
 */
class UpgradeComponent extends Gw2Api  {

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);
		
        $this->sub_type      = empty( $APIItem['details']['type'] ) ? null : $APIItem['details']['type'];
		$this->bonuses       = empty( $APIItem['details']['bonuses'])        ? array() : $APIItem['details']['bonuses'];
		$this->infix_upgrade = empty( $APIItem['details']['infix_upgrade'] ) ? array() : $APIItem['details']['infix_upgrade'];
		$this->attributes_array = $this->getAttributesArray();
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
		{$this->getBonuses()}
		<div>{$this->rarity}</div>
		{$this->getLevelDisplay()}
		{$this->getDescriptionDisplay()}
		{$this->getSoulboundStatus()}
		{$this->getVendorValueDisplay()}
HTML;
	}

	public function getSubTypeInsert( ) {
		global $aUpgrade_types;
		return (int) array_search( $this->sub_type, $aUpgrade_types );
	}

	public function getAttributesInsert() {
		// used by runes
		if( !empty( $this->bonuses ) )
			return json_encode( $this->bonuses );
		
		// used by sigils
		if( !empty( $this->infix_upgrade['buff']['description'] ) )
			return $this->infix_upgrade['buff']['description'];
		return;
	}

}	
?>