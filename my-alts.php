<?php if( !$user->isLoggedIn( ) ) die() ?>


<div class="section group">
	<div class="col span_2_of_3">

		<div class="content-box">
			<h6>My Characters</h6>
			Your legacy lies here. create more characters, edit, delete or update them. Either way the power to create an army is yours.
		</div>
		<div class="content-box">
			<?php include( FILE_CRETE_ALT );?>
		</div>

		<div class="content-box">
			<h6>Manage Characters</h6>

			<div class="title_box" id="bill_to">
			<?php
			if( isset( $_GET['edit'] ) && is_array( $altData = $alts->getAltInfo( $_GET['edit'], true ) ) )
			{
			?>
			   <fieldset>
			        <legend><?=$altData['name']?></legend>
		        	<form method="post" action="includes/Process.class.php">
					<input name="todo" type="hidden" value="updateAlt">
					<input name="ajaxrequest" type="hidden" value="1">
					<input name="updateAlt" type="hidden" value="1">
					<input name="character_id" type="hidden" value="<?=$altData['id']?>">

			        <div><label>Name</label><input type="text" name="character_name" value="<?=$altData['name']?>"  required pattern="[a-zA-Z ]+" maxlength="19" /><span class="character_name error_msg"></span></div>	
					<div><label>Race</label><?=$aRace[$altData['race']]?></div>
					<div><label>Gender</label><?=tools::simpleDropMenu( $aGender, 'gender' )?></div>
					<div><label>Profession</label><?=$aProfession[$altData['profession']]?></div>
					<div><label>Level</label><?=tools::simpleDropMenu( $aLevel, 'level', $altData['level'] )?></div>
			        <div><label></label><input type="submit" value="Save"></div>
			        </form>

			    </fieldset>
			<?php } else { ?>
				Select a Character to Edit.
			<?php } ?>
			</div>
		</div>
	</div>
	<div class="col span_1_of_3 content-box">
	<?=$userAlts;?>
	</div>	

</div>




