<?php
	// Report all PHP errors
	//error_reporting(E_ALL);
	
	// Mysql
	define('MYSQL_HOST', 'localhost');
	define('MYSQL_USER', 'root');
	define('MYSQL_PASS', 'admin');
	define('MYSQL_DB',   'gw2alts');

	define('URL_API',    'https://api.guildwars2.com/v2/items/');
	define('URL_ITEMS',  'items/');
	define('URL_ITEM',   'URL_ITEMS'); 
	define('URL_RENDER', 'https://render.guildwars2.com/file/');

	define('FILE_REGISTER_FORM',  '/includes/members/register-form.php');
	define('FILE_LOGGED_IN',      '/includes/members/loggedin-form.php');
	define('FILE_PROFILE_UPDATE', '/includes/members/update-profile.php');
	define('FILE_CRETE_ALT',      '/includes/alts/create-alt.php');

	define('DIR_ALT_FACE',        'images/alt_face/');	

	define('TBL_ITEMS', 'items');
	define('TBL_ITEM_TYPE', 'item_type');	
	define('TBL_ITEM_TYPE_ARMOR', 'item_type_armor');	
	define('TBL_ITEM_TYPE_ARMOR_TYPE', 'item_type_armor_type');
	
	define('PAG_PER_PAGE', 10);	

	$aItem_types 				= array( 1 =>'Armor','Back','Bag','Consumable','Container','CraftingMaterial','Gathering','Gizmo','MiniPet','Tool','Trait','Trinket','Trophy','UpgradeComponent','Weapon'); 
	$aRarities 					= array( 1 =>'Ascended','Basic','Exotic','Fine','Junk','Legendary','Masterwork','Rare');
	$aDisciplines 				= array( 1 =>'Armorsmith','Artificer','Chef','Huntsman','Jeweler','Leatherworker','Tailor','Weaponsmith');
	$aWeapon_types 				= array( 1 =>'Axe', 'Dagger', 'Focus', 'Greatsword', 'Hammer', 'Harpoon', 'LargeBundle', 'LongBow', 'Mace', 'Pistol', 'Rifle', 'Scepter', 'Shield', 'ShortBow', 'Speargun', 'Staff', 'Sword', 'Torch', 'Toy', 'Trident', 'TwoHandedToy', 'Warhorn');
	$aWeapon_dmg_types 			= array( 1 =>'Choking','Fire','Ice','Lightning','Physical');
	$aInfusion_flags 			= array( 1 =>'Defense','Offense','Utility');
	$aArmor_weights 			= array( 1 =>'Clothing','Light','Medium','Heavy');
	$aArmor_types 				= array( 1 =>'Boots','Coat','Gloves','Helm','HelmAquatic','Leggings','Shoulders');
	$aAttributes 				= array( 1 =>'Agony Resistance','Boon Duration','Condition Damage','Condition Duration','Ferocity','Healing Power','Magic Find','Power','Precision','Toughness','Vitality');
	$aConsumable_types 			= array( 1 =>'AppearanceChange','Booze','ContractNpc','Food','Generic','Halloween','Immediate','Transmutation','Unlock','UnTransmutation','UpgradeRemoval','Utility');
	$aConsumable_unlock_types 	= array( 1 =>'BagSlot','BankTab','CollectibleCapacity','Content','CraftingRecipe','Dye','Unknown');
	$aContainer_types 			= array( 1 =>'Default','GiftBox','OpenUI');
	$aGathering_types 			= array( 1 =>'Foraging','Logging','Mining');
	$aGizmo_types 				= array( 1 =>'Default','ContainerKey','RentableContractNpc','UnlimitedConsumable');
	$aTool_types 				= array( 1 =>'Salvage');
	$aTrinket_types 			= array( 1 =>'Amulet','Accessory','Ring');
	$aUpgrade_types 			= array( 1 =>'Default','Gem','Rune','Sigil');
	$aFlags 					= array( 1 =>'AccountBindOnUse', 'AccountBound','HideSuffix','MonsterOnly','NoMysticForge','NoSalvage','NoSell','NotUpgradeable','NoUnderwater','SoulbindOnAcquire','SoulBindOnUse','Unique');
	$aGame_types 				= array( 1 =>'Activity','Dungeon','Pve','Pvp','PvpLobby','Wvw');
	$aRestrictions 				= array( 1 =>'Asura','Charr','Human','Norn','Sylvari','Guardian','Warrior','Mesmer','Elementalist','Thief','Engineer','Necromancer','Ranger');

	$aRace 						= array( 1 =>'Asura', 'Charr','Human','Norn','Sylvari' );
	$aGender 					= array( 1 =>'Male', 'Female' );
	$aProfession 				= array( 1 =>'Elementalist','Engineer','Guardian','Mesmer','Necromancer','Ranger','Thief','Warrior' );
	$aLevel 				    = array_combine( range( 1,80 ), range( 1,80 ) )

?>