<?php
if( basename( $_SERVER['PHP_SELF'] ) !== 'index.php' )
	die(' Acced Denied ');
?>
	<p class="tbl-header">Logged in as : <?=$_SESSION['user_name']?></p>
	<div class="hr2"></div>
	<form method="post" action="includes/Process.class.php">
	<input name="todo" type="hidden" value="logOutUser">
	<input name="ajaxrequest" type="hidden" value="1">
	<input name="logout" type="hidden" value="1">
	<p><label>Log out</label>  <input type="submit" value="Log out"></p>
	</form>