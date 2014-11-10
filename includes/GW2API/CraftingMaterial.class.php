<?php
/*
 * class CraftingMaterial  extends Gw2Api 
 */
class CraftingMaterial  extends Gw2Api  {	

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);	
    }

     //TODO: Get crafting material ingredient-info to look like:
    //Crafting Ingredient: Huntsman(400), Artificer(400), Armorsmith(400), Leatherworker(400), Tailor(400)

}
?>