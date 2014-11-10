<?php
  // testing
  function print_r2( $aVal ){
        echo '<pre><hr>';
        print_r($aVal);
        echo  '</pre><hr>';
  }

	error_reporting(E_ALL);
	$mainDir = dirname(__FILE__);
	include( $mainDir .'/define.php' );
	include( $mainDir .'/autoloader.php' );

	// Opn the database
	$db = new MySQL( MYSQL_DB, MYSQL_USER, MYSQL_PASS, MYSQL_HOST );

	// New User
	$user = new User;
	
	// New Alts
	$alts = new Gw2Alts;

	if( $user->isLoggedIn( ) )
	{
		
		$userAlts = $alts->getUserAlts( $user->getUserId() );
		$userData = (object) $user->getUserInfo();
	}
?>