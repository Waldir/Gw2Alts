<?php
if( basename( $_SERVER['PHP_SELF'] ) !== 'index.php' )
	die(' Acced Denied ');
?>

<div id="Create-alt-Form">
	<h6>Create new character</h6>
	<form method="post" action="includes/Process.class.php">
	<input name="todo" type="hidden" value="createAlt">
	<input name="ajaxrequest" type="hidden" value="1">
	<input name="createAlt" type="hidden" value="1">

	<div><label>Name</label><input type="text" name="character_name" placeholder="Gw2 Character name" required pattern="[a-zA-Z ]+" maxlength="19" /><span class="character_name error_msg"></span></div>
	<div><label>Race</label><?=tools::simpleDropMenu( $aRace, 'race' )?></div>
	<div><label>Gender</label><?=tools::simpleDropMenu( $aGender, 'gender' )?></div>
	<div><label>Profession</label><?=tools::simpleDropMenu( $aProfession, 'profession' )?></div>
	<div><label>Level</label><?=tools::simpleDropMenu( $aLevel, 'level' )?></div>
	<div><label></label><input type="submit" value="Save"></div>
	</form>
</div>