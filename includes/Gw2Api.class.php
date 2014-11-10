<?php
/*
 * Gw2Api.class.php
 */
 
class Gw2Api 
{
	// Common properties
    protected $item_id; 					// 1
    protected $name; 						// 2
    protected $description; 				// 3
    protected $type; 						// 4
    protected $sub_type; 					// 5
    protected $level; 						// 6
    protected $rarity; 						// 7
    protected $vendor_value; 				// 8
    protected $img_file; 				    // 9
    protected $game_types; 					// 11
    protected $flags; 						// 12
    protected $restrictions; 				// 13

	// Armor Specific.
	protected $weight_class;
	
	// Shared	
    protected $defense;
    protected $default_skin ;
    protected $infusion_slots;
    protected $infix_upgrade;
    protected $attributes_array;
    protected $suffix_item_id;
    protected $secondary_suffix_item_id;
	
	// weapon
	protected $damage_type;
    protected $min_power;
    protected $max_power;
	
	// tool
	protected $charges;
	
	// consumable
    protected $duration_ms;
    protected $sub_description;

	// bag
    protected $no_sell_or_sort;
    protected $size;

	  /*****************/
	 /* Constructor   */
	/*****************/	
	protected function __construct( $APIItem )
	{
		// Common properties defiened.
	    $this->item_id          = (int) $APIItem['id'];
        $this->name             = $APIItem['name'];
        $this->description      = isset( $APIItem['description'] ) ? $APIItem['description'] : '';
        $this->type             = $APIItem['type'];
		$this->sub_type         = null;
        $this->level            = $APIItem['level'];
        $this->rarity           = $APIItem['rarity'];
        $this->vendor_value     = (int) $APIItem['vendor_value'];
        $this->img_file         = $APIItem['icon'];
        $this->game_types       = $APIItem['game_types'];
        $this->flags            = $APIItem['flags'];
        $this->restrictions     = $APIItem['restrictions'];
		$this->attributes_array = array();
		

	}
	// Getters need to clean
    public function getItemId() 
    { 
    	return $this->item_id; 
    }

	public function getName() 
	{ 
		return $this->name; 

	}
 	public function getDescription() 
 	{ 
 		return $this->description;
 	}

	public function getType() 
	{ 
		return $this->type; 
	}

	public function getTypeSubmit() 
	{ 
		global $aItem_types; 
		return array_search( $this->type, $aItem_types ); 
	}

	public function getSubType() 
	{ 
		return $this->sub_type; 
	}

	public function getDefaultSkin() 
	{ 
		return $this->default_skin; 
	}	

	public function getLevel() 
	{ 
		return $this->level; 
	}

	public function getRarity() 
	{ 
		return $this->rarity; 
	}

	public function getRarityInsert() 
	{ 
		global $aRarities; 
		return array_search( $this->rarity, $aRarities ); 
	}

    public function getVendorValue() 
    { 
    	return $this->vendor_value; 
    }

    public function getImgFIle() 
    { 
    	return $this->img_file; 
    }

	public function getSuffixId() 
	{ 
		return $this->suffix_item_id; 
	}

	public function getSecondSuffixItem() 
	{ 
		return $this->secondary_suffix_item_id; 
	}

     public function getDefenseDisplay( )
    {
        return $this->getDefense() > 0 ?  "<div>Defense: <span class='Masterwork'>{$this->getDefense()}</span></div>" : '';
    }

    protected function getLevelDisplay() {
        return ( $this->level > 0 ) ? "<div>Required Level: {$this->level}</div>" : null;
    }

	public function getVendorValueDisplay(){
		if( $cash = tools::getGw2Money( $this->vendor_value ) )
			return '<div>'. $cash.'</div>';
		return;
	}

  	public function getDescriptionDisplay() { 
		return strpos( $this->getDescription(), '@flavor' ) ? '<div class="Flavor">' . $this->getDescription() . '</div>': '<div>'. $this->getDescription() . '</div>';
	}

	/**
	* getAttributesArray()
	* 	Returns an array of attributes
	* 	In some cases a string with a description
	*
	* 	@return $aFixed_infix
	*/
	public function getAttributesArray() 
	{
		$aFixed_infix = array();

		if( @$atts = $this->infix_upgrade['attributes'] )
		{
			// clean the attributes.
			$atts = $this->cleanAttributes( $this->infix_upgrade['attributes'] );
			// fix the format of the attributes.
			$aFixed_infix = array_column( $atts, 'modifier', 'attribute' );	
		}
		
		// description is available
		if( @$buffs = $this->infix_upgrade['buff']['description'] )
			$aFixed_infix = $this->getAttributesFromDescription( $buffs, $aFixed_infix );

		return $aFixed_infix;
	}
	
	public function getAttributesFromDescription( $sDesc, $addToArray = array() )
	{
		// description has to be in the right format
		if ( $sDesc[0] == "+" )
		{
			// pull the buffs description
			$buffs = explode( "\n", $sDesc );
			
			$aDesc_infix = array();
			// format it to look like the other stuff
			foreach ( $buffs as $buff )
			{
				list($modifier, $attribute) = explode(' ', $buff, 2);
				$modifier =       str_replace( '+', '', $modifier);
				$modifier = (int) str_replace( '%', '', $modifier);
				
				// if the att is present add it up else just join it
				if( in_array( $attribute, array_keys( $addToArray ) ) )
					$addToArray[$attribute] += $modifier;
				else 
					$addToArray[$attribute] = $modifier;		
			}
			return $addToArray;
		}
		return $sDesc;
	}

	protected function cleanAttributes( $array ) {
		//Rename certain attributes to be in line with how they appear in game.
		array_walk( $array, function( &$attr ){
			if ($attr['attribute'] == 'CritDamage') $attr['attribute'] = 'Ferocity';
			if ($attr['attribute'] == 'Healing')    $attr['attribute'] = 'Healing Power';
			$attr['attribute'] = preg_replace( '/(?<! )(?<!^)[A-Z]/',' $0', $attr['attribute'] );
		});
		return $array;
    }
	
	public function getAttributesDisplay() {
		if( empty( $this->attributes_array ) )
			return;
		
		// not an array
		if( !is_array( $this->attributes_array ) )
			return '<div class="Fine">'.$this->attributes_array."</div><br />";

		// loop and find the correct attributes.
		$toReturn = '';
		foreach( $this->attributes_array as $sKey => $iValue )
			$toReturn .= "<div class='Masterwork'>+{$iValue} {$sKey}</div>";
		return $toReturn . '<br />';	
	}
	
	public function getAttributesInsert() {
		global $aAttributes;
		if( empty( $this->attributes_array ) )
			return;
			
		// loop and find the correct attributes.
		$toReturn = array();
		foreach( $this->attributes_array as $sKey => $iValue )
		{
			$atts = array_search( $sKey, $aAttributes );
			if( !empty( $atts ) )
				$toReturn[$atts] = $iValue;
		}
		return json_encode( $toReturn );	
	}
	
    public function getGameTypes() {
		global	$aGame_types;
		if( empty( $this->game_types ) )
			return;		
		
		$aToReturn = array_intersect( $aGame_types, $this->game_types );
		return json_encode( array_keys( $aToReturn ) ); // Create json object
	}
	
	public function getFlags() {
		global	$aFlags;
		if( empty( $this->flags ) )
			return;
			
		$aToReturn = array_intersect( $aFlags, $this->flags );
		return json_encode( array_keys( $aToReturn ) ); // Create json object
	}
	
	public function getRestrictions() {
		global	$aRestrictions;
		if( empty( $this->restrictions ) )
			return;
			
		$aToReturn = array_intersect( $aRestrictions, $this->restrictions );
		return json_encode( array_keys( $aToReturn ) ); // Create json object
	}
	
    public function getInfusionSlotsInsert( ){
		// make the infusion flgas array usable
		global $aInfusion_flags;
		
		// if there are no infusions stop here
		if( empty( $this->infusion_slots ) ) 
			return;
		
		// gather the flag.  (Defense and so on)
		$infusionFlag = $this->infusion_slots[0]['flags'][0];
		
		// cross relation with insuions flags
		$infusions[] = array_search( $infusionFlag, $aInfusion_flags );
		
		// if there is a second infuion add it.
		if( !empty( $this->infusion_slots[1]['flags'][0] ) )
			$infusions[] = $infusions[0];
		
		// return a json string
		return json_encode( $infusions );
	}

    public function getInfusionSlotsDisplay( ){
		// if there are no infusions stop here
		if( empty( $this->infusion_slots ) ) 
			return;

		$toReturn = '';
		// gather the flag.  (Defense and so on)
		if( !empty( $this->infusion_slots[0]['flags'][0] ) )
			$toReturn .= '<div><span class="Infusion-Slot">I</span> Unused '.$this->infusion_slots[0]['flags'][0].' Infusion Slot</div><br />';

		if( !empty( $this->infusion_slots[1]['flags'][0] ) )
			$toReturn .= '<div><span class="Infusion-Slot">I</span> Unused '.$this->infusion_slots[1]['flags'][0].' Infusion Slot</div><br />';

		return $toReturn;

    }

	public function getTooltip() 
	{
		return <<<HTML
		<div>
			<img src="{$this->img_file}" class="item-icon-tooltip">
			<span class="{$this->rarity} item_title">{$this->getName()}</span>
		</div>
		{$this->getInfusionSlotsDisplay( )}
		{$this->getDescriptionDisplay( )}
		<div class="Junk">{$this->type}</div>
		{$this->getSoulboundStatus( )}
		{$this->getVendorValueDisplay( )}
HTML;
    }
	
	public function getSoulboundStatus() {
		$toReturn = null;
        if ( in_array( "SoulBindOnUse",     $this->flags ) ) $toReturn = "Soulbound On Use"; 
        if ( in_array( "AccountBound",      $this->flags ) ) $toReturn = "Account Bound"; 
        if ( in_array( "SoulBindOnAcquire", $this->flags ) ) $toReturn = "Soulbound On Acquire"; 
        
        return empty( $toReturn ) ? null : '<div>'.$toReturn.'</div>';
    }

    public function getBonuses( $aBonus = array() ) {
    	if( empty( $aBonus ) )
    		if( empty( @$aBonus = $this->bonuses ) )
    			return;

		$html = '';
		foreach( $aBonus as $iBonus => $bonus )
		{
			$i = $iBonus + 1;
			$html .= "<div class='Fine'>({$i}) {$bonus}</div>" . PHP_EOL ;
		}
        
        return $html . '<br />';
    }

    protected function getSuffixDisplay() {
		if( empty( $this->suffix_item_id ) )
			return;
		
        $Suffix_Item = Gw2Api::getItemById( $this->suffix_item_id);

		$html = "<div><img src='{$Suffix_Item->img_file}' height='16' width='16' class='rune_img'> <span class='Fine'>{$Suffix_Item->getName()}</span></div>";

		// its a rune
		if( !empty( $Suffix_Item->bonuses ) )
		{
			$html .= $this->getBonuses( $Suffix_Item->bonuses );
			return $html;
		}

		// description that contains buff is present
		if( !empty( $Suffix_Item->infix_upgrade['buff']['description'] ) )
		{
			$buffs = $this->getAttributesFromDescription( $Suffix_Item->infix_upgrade['buff']['description'] );

			if( !is_array( $buffs ) )
			{
				$html .= "<div class='Fine'>".$buffs. '</div>';
				return $html . '<br />';
			}

			foreach( $buffs as $iBuff => $sBuff )
				$html .= "<div class='Fine'>+{$sBuff} {$iBuff}</div>" . PHP_EOL ;
			return $html . '<br />';
		}
        return;
    }

	/**
	* getItemsFromApi()
	* 	Returns an array of all the item ids
	* 	from the API
	*
	* 	@return $obj
	*/
	public static function getItemsFromApi ()
	{
		$json = file_get_contents( URL_API );
		$obj = json_decode($json, true);
		return $obj;
	}

	/**
	* getItemById()
	* 	Creates a new object from the API item number
	* 	depending on the item type
	*
	* 	@return new ItemType Class
	*/
	public static function getItemById( $itemNumer )
	{
		// create the array from the API item number.
		$APIItem = @json_decode( file_get_contents( URL_API . $itemNumer ), true );
		
		// if the array is not in the right format terminate.
		if( $APIItem === null && json_last_error() !== JSON_ERROR_NONE )
			return false;

		// if there is no item type terminate
		if( !isset( $APIItem['type'] ) )
			return null;

		// create a new object depending on the item type
		switch( $APIItem['type'] ) 
		{
		case "Armor":            return new Armor           ( $APIItem );
		case "Back":             return new Back            ( $APIItem );
		case "Bag":              return new Bag             ( $APIItem );
		case "Consumable":       return new Consumable      ( $APIItem );
		case "Container":        return new Container       ( $APIItem );
		case "CraftingMaterial": return new CraftingMaterial( $APIItem );
		case "Gathering":        return new Gathering       ( $APIItem );
		case "Gizmo":            return new Gizmo           ( $APIItem );
		case "MiniPet":          return new MiniPet         ( $APIItem );
		case "Tool":             return new Tool            ( $APIItem );
		case "Trinket":          return new Trinket         ( $APIItem );
		case "Trophy":           return new Trophy          ( $APIItem );
		case "UpgradeComponent": return new UpgradeComponent( $APIItem );
		case "Weapon":           return new Weapon          ( $APIItem );
		default:                 return null;
		}
		
	} // function getItemById

} // class end

/* Initialize Gw2Api */
//$oGw2Api = new Gw2Api( );
?>