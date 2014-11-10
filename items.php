<?php
$items = new Items;
?>
<div class="content-box">
	<form action="" method="get" id="filterForm" />
	<input name="p" value="items" type="hidden" />
	<input name="searchFrom" id="searchFrom" value="items" type="hidden" disabled="disabled"  />
	<input name="searchWhat" id="searchWhat" value="name" type="hidden"  disabled="disabled" />
	<input name="sort"  value="<?=$items->getSort()?>"   type="hidden" />
	<input name="order" value="<?=$items->getOrder()?>" type="hidden" />
	<input name="search" id="searchItem" type="text" placeholder="Search" value="<?=stripslashes( $items->getSearch() );?>" />
	<span class="vr"></span>
	<?=tools::dropMenu( $aRarities, 'rarity', $items->getRarity() )?>
	<span class="vr"></span>
	<?=tools::dropMenu( $aItem_types, 'type', $items->getType() )?>
	<span class="vr"></span>
	<input value="Search" type="submit" name="" /> 
	<span class="vr"></span>
	Total items: <?=$items->getTotalItems()?>
	</form>
</div>

<div class="content-box">
<?php
echo $items->getItemsList();
?>

</div>