<?php
if( basename( $_SERVER['PHP_SELF'] ) !== 'index.php' )
	die(' Acced Denied ');
?>

<div id="User-Login-Form">
	<p class="tbl-header">Existing Users: Log in</p>
	<div class="hr2"></div>
	<form method="post" action="includes/Process.class.php">
	<input name="todo" type="hidden" value="logInUser">
	<input name="ajaxrequest" type="hidden" value="1">
	<input name="login" type="hidden" value="1">

	<p><label for="userName">Username/E-mail</label><input type="text" name="userName" placeholder="Username or Email" pattern=".{5,}" required /></p>
	<p><label for="password">Password</label><input type="password" name="userPassword" placeholder="Password" pattern=".{6,}" required /></p>
	<p><label></label><input type="submit" value="Login"></p>
	</form>
</div>

<br />

<div id="User-Register-Form">
	<p class="tbl-header">New Users: Register</p>
	<div class="hr2"></div>
	<form method="post" action="includes/Process.class.php" autocomplete="off">
	<input name="todo" type="hidden" value="registerUser">
	<input name="ajaxrequest" type="hidden" value="1">
	<input name="register" type="hidden" value="1">

	<p><label>Your email address</label><input type="text"     name="newEmail"         placeholder="Your email address" required /> </p>
	<p><label>Choose a username</label><input  type="text"     name="newUsername"      placeholder="2 to 64 characters long" pattern=".{5,}" required /></p>
	<p><label>Choose a password</label><input  type="password" name="newPassword"      placeholder="6 characters or longer"  pattern=".{6,}" required autocomplete="off"></p>
	<p><label>Re-enter Password</label><input  type="password" name="newPasswordCheck" placeholder="6 characters or longer"  pattern=".{6,}" required autocomplete="off" /></p>
	<p><label></label><input type="submit" value="Register"></p>
	</form>
</div>