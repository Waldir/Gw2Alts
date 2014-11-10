<div class="content-box">
	<form action="" method="get" id="filterForm" />
	<input name="p" value="alts" type="hidden" />
	<input name="search" id="searchItem" value="<?=$alts->getSearch();?>"       type="text" placeholder="Search"  />
	<input name="searchFrom" id="searchFrom" value="alts" type="hidden" disabled="disabled" />
	<input name="searchWhat" id="searchWhat" value="name" type="hidden" disabled="disabled" />
	<input value="Search" type="submit" /> 
	Total items: <?=$alts->getTotalItems()?>
	</form>
</div>

<div class="content-box">
	<?=$alts->getAllAlts();?>
</div>