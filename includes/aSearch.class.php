<?php

class aaSearch
{
	private $haystack;
	private $needle; 
	private $ars = array(

	'itemTypes'							=> array( 1 =>'Armor','Back','Bag','Consumable','Container','CraftingMaterial','Gathering','Gizmo','MiniPet','Tool','Trait','Trinket','Trophy','UpgradeComponent','Weapon'),
	'rarities'							=> array( 1 =>'Ascended','Basic','Exotic','Fine','Junk','Legendary','Masterwork','Rare'),
	'disciplines'						=> array( 1 =>'Armorsmith','Artificer','Chef','Huntsman','Jeweler','Leatherworker','Tailor','Weaponsmith'),
	'waponTypes'						=> array( 1 =>'Axe', 'Dagger', 'Focus', 'Greatsword', 'Hammer', 'Harpoon', 'LargeBundle', 'LongBow', 'Mace', 'Pistol', 'Rifle', 'Scepter', 'Shield', 'ShortBow', 'Speargun', 'Staff', 'Sword', 'Torch', 'Toy', 'Trident', 'TwoHandedToy', 'Warhorn'),
	'weaponDmgTypes'				=> array( 1 =>'Choking','Fire','Ice','Lightning','Physical'),
	'infusionFlags'					=> array( 1 =>'Defense','Offense','Utility'),
	'armorWeights'					=> array( 1 =>'Clothing','Light','Medium','Heavy'),
	'armorTypes'						=> array( 1 =>'Boots','Coat','Gloves','Helm','HelmAquatic','Leggings','Shoulders'),
	'attributes'						=> array( 1 =>'Agony Resistance','Boon Duration','Condition Damage','Condition Duration','Ferocity','Healing Power','Magic Find','Power','Precision','Toughness','Vitality'),
	'consumableTypes'				=> array( 1 =>'AppearanceChange','Booze','ContractNpc','Food','Generic','Halloween','Immediate','Transmutation','Unlock','UnTransmutation','UpgradeRemoval','Utility'),
	'consumableUnlockTypes'	=> array( 1 =>'BagSlot','BankTab','CollectibleCapacity','Content','CraftingRecipe','Dye','Unknown'),
	'containerTypes'				=> array( 1 =>'Default','GiftBox','OpenUI'),
	'gatheringTypes'				=> array( 1 =>'Foraging','Logging','Mining'),
	'gizmoTypes'						=> array( 1 =>'Default','ContainerKey','RentableContractNpc','UnlimitedConsumable'),
	'toolTypes'							=> array( 1 =>'Salvage'),
	'trinketTypes'					=> array( 1 =>'Amulet','Accessory','Ring'),
	'upgradeTypes'					=> array( 1 =>'Default','Gem','Rune','Sigil'),
	'flags'									=> array( 1 =>'AccountBindOnUse', 'AccountBound','HideSuffix','MonsterOnly','NoMysticForge','NoSalvage','NoSell','NotUpgradeable','NoUnderwater','SoulbindOnAcquire','SoulBindOnUse','Unique'),
	'gameTypes'							=> array( 1 =>'Activity','Dungeon','Pve','Pvp','PvpLobby','Wvw'),
	'restrictions'					=> array( 1 =>'Asura','Charr','Human','Norn','Sylvari','Guardian','Warrior','Mesmer','Elementalist','Thief','Engineer','Necromancer','Ranger'),

	'race'									=> array( 1 =>'Asura', 'Charr','Human','Norn','Sylvari' ),
	'gender'								=> array( 1 =>'Male', 'Female' ),
	'profession'						=> array( 1 =>'Elementalist','Engineer','Guardian','Mesmer','Necromancer','Ranger','Thief','Warrior' ) );

	public function __construct( $haystack, $needle )
	{
		$this->haystack = $haystack;
		$this->needle   = $needle;

		if( is_array( $this->ars[$haystack] ) && !empty( $this->ars[$haystack] )  )
			if( is_numeric( $needle ) )
				return $this->getNameFromId();
			else
				return $this->getIdFromName();
	}

	public function getIdFromName( )
	{
			if( $result = array_search( $this->needle, $this->ars[$this->haystack] ) )
				return $result;

		// else 
		return false;
	}

	public function getNameFromId( )
	{
		// if its an array and if it is not empty
			if( !empty( $this->ars[$haystack][$needle] ) )
				return $this->ars[$haystack][$needle];

		// else 
		return false;

	}

}



// Declare a simple class
class aSearch
{
	public $haystack;
	public $needle; 
	public $toString;
	private $ars = array(

	'itemTypes'							=> array( 1 =>'Armor','Back','Bag','Consumable','Container','CraftingMaterial','Gathering','Gizmo','MiniPet','Tool','Trait','Trinket','Trophy','UpgradeComponent','Weapon'),
	'rarities'							=> array( 1 =>'Ascended','Basic','Exotic','Fine','Junk','Legendary','Masterwork','Rare'),
	'disciplines'						=> array( 1 =>'Armorsmith','Artificer','Chef','Huntsman','Jeweler','Leatherworker','Tailor','Weaponsmith'),
	'waponTypes'						=> array( 1 =>'Axe', 'Dagger', 'Focus', 'Greatsword', 'Hammer', 'Harpoon', 'LargeBundle', 'LongBow', 'Mace', 'Pistol', 'Rifle', 'Scepter', 'Shield', 'ShortBow', 'Speargun', 'Staff', 'Sword', 'Torch', 'Toy', 'Trident', 'TwoHandedToy', 'Warhorn'),
	'weaponDmgTypes'				=> array( 1 =>'Choking','Fire','Ice','Lightning','Physical'),
	'infusionFlags'					=> array( 1 =>'Defense','Offense','Utility'),
	'armorWeights'					=> array( 1 =>'Clothing','Light','Medium','Heavy'),
	'armorTypes'						=> array( 1 =>'Boots','Coat','Gloves','Helm','HelmAquatic','Leggings','Shoulders'),
	'attributes'						=> array( 1 =>'Agony Resistance','Boon Duration','Condition Damage','Condition Duration','Ferocity','Healing Power','Magic Find','Power','Precision','Toughness','Vitality'),
	'consumableTypes'				=> array( 1 =>'AppearanceChange','Booze','ContractNpc','Food','Generic','Halloween','Immediate','Transmutation','Unlock','UnTransmutation','UpgradeRemoval','Utility'),
	'consumableUnlockTypes'	=> array( 1 =>'BagSlot','BankTab','CollectibleCapacity','Content','CraftingRecipe','Dye','Unknown'),
	'containerTypes'				=> array( 1 =>'Default','GiftBox','OpenUI'),
	'gatheringTypes'				=> array( 1 =>'Foraging','Logging','Mining'),
	'gizmoTypes'						=> array( 1 =>'Default','ContainerKey','RentableContractNpc','UnlimitedConsumable'),
	'toolTypes'							=> array( 1 =>'Salvage'),
	'trinketTypes'					=> array( 1 =>'Amulet','Accessory','Ring'),
	'upgradeTypes'					=> array( 1 =>'Default','Gem','Rune','Sigil'),
	'flags'									=> array( 1 =>'AccountBindOnUse', 'AccountBound','HideSuffix','MonsterOnly','NoMysticForge','NoSalvage','NoSell','NotUpgradeable','NoUnderwater','SoulbindOnAcquire','SoulBindOnUse','Unique'),
	'gameTypes'							=> array( 1 =>'Activity','Dungeon','Pve','Pvp','PvpLobby','Wvw'),
	'restrictions'					=> array( 1 =>'Asura','Charr','Human','Norn','Sylvari','Guardian','Warrior','Mesmer','Elementalist','Thief','Engineer','Necromancer','Ranger'),

	'race'									=> array( 1 =>'Asura', 'Charr','Human','Norn','Sylvari' ),
	'gender'								=> array( 1 =>'Male', 'Female' ),
	'profession'						=> array( 1 =>'Elementalist','Engineer','Guardian','Mesmer','Necromancer','Ranger','Thief','Warrior' ) );

	public function __construct( $haystack, $needle ) 
	{
		$this->haystack = $haystack;
		$this->needle   = $needle;

		if( @is_array( $this->ars[$haystack] ) && !empty( $this->ars[$haystack] )  )
			if( is_numeric( $needle ) )
				return $this->getNameFromId();
			else
				return $this->getIdFromName();
	}

	public function getIdFromName( )
	{

		return $result = array_search( $this->needle, $this->ars[$this->haystack] );
			return $result;

		// else 
		return false;
	}

	public function getNameFromId( )
	{
		print_r($this->ars[$this->haystack][$this->needle]);
		// if its an array and if it is not empty
		if( !empty( $this->ars[$this->haystack][$this->needle] ) )
			return $this->ars[$haystack][$needle];

		// else 
		return 'false';

	}

}