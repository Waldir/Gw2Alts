<?php
if( basename( $_SERVER['PHP_SELF'] ) !== 'index.php' )
	die(' Acced Denied ');
?>
	<h3>Edit Profile : <?=$userData->user_name;?></h3>
	<p class="hr2"></p>
	<form method="post" action="includes/Process.class.php">
	<input name="todo" type="hidden" value="updateProfile">
	<input name="ajaxrequest" type="hidden" value="1">

	
	<p class="tbl-header">Edit email</p>
	<p class="hr2"></p>
	<input name="update" type="hidden" value="1">
	<p><label>Email:</label><input type="text" name="newEmail" value="<?=$userData->user_email;?>"></p>
	<p><label></label>  <input type="submit" value="Save"></p>

	<p class="br">s</p>

	<p class="tbl-header">Change password</p>
	<p class="hr2"></p>	
	<p><label>Current password</label><input type="password" name="currentPassword" placeholder="leave blank to keep current" autocomplete="off"></p>
	<p><label>New password</label><input type="password" name="newPassword" autocomplete="off"></p>
	<p><label>Re-enter password</label><input type="password" name="newPasswordCheck" autocomplete="off"></p>
	<p><label></label>  <input type="submit" value="Save"></p>
	</form>