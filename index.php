<?php
include ('includes/includes.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Place holder title</title>
	<meta name="robots" content="all, index, follow" />
	<meta name="distribution" content="global" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<link rel="stylesheet" type="text/css" media="all" href="css/main.css" />
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.3.11/slick.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="https://code.jquery.com/ui/jquery-ui-1-9-git.css" /> 
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js" /></script>
	<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js" /></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js" /></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.3.11/slick.min.js" /></script>
	<script type="text/javascript" src="js/jquery.cycle2.js" /></script>
	<script type="text/javascript" src="js/js.js" /></script>
</head>

<body>
<!-- MSG BOX -->
<div id="message_box">
	<p id="error_success"></p>
	<div class="hr2"></div>
	<p id="message_box_msg"></p>
	<button type="button" name="close-msg"> Close </button>
	<p class="br">s</p>
</div>
<!-- MSG BOX -->

<!-- Scroll to Top Button -->
<div class="scrollTop"><a href="">^</a></div>
<!-- Scroll to Top Button -->

<div id="wrapper">
<header>
	<div class="container">
		<!-- Navigation -->
		<nav id="topNavMenu">
			<ul>
			   <li><a href="index.php" class='left'>Home</a></li>
			   <li><a href="?p=items" class='left'>Items</a></li>
			   <li><a href="?p=alts" class='left'>Characters</a></li>
			   <li><a href='#' class='left'>Contact</a></li>



			   <li class="right ShowMenu" data-menu="User-Menu">
			   	<?=( $user->isLoggedIn( ) ) ? '<span class="sleep NavIcon">Logout</span>' : '<span class="user NavIcon">User</span>';?>
					<div id="User-Menu">
					<?php
						if ( $user->isLoggedIn() )
							include( FILE_LOGGED_IN ); 
						else
							include( FILE_REGISTER_FORM ); 
						?>
					</div>
				</li>
				<?php
			   if( $user->isLoggedIn( ) ){
			   ?> 
			   	<li class="right ShowMenu" data-menu="Settings-Menu">
			   		<span class="settings NavIcon">Settings</span>
			   		<div id="Settings-Menu">
						<?php include( FILE_PROFILE_UPDATE ); ?>
					</div>
				</li>
			   	<li class="right ShowMenu" data-menu="Alts-Menu">
			   		<span class="alts NavIcon" >Alts</span>
			   		<div id="Alts-Menu">
						<?php include( FILE_CRETE_ALT ); ?>
						<h6>My Characters</h6>
						&#149; <a href="?p=my-alts">Manage</a>
					</div>
				</li>		
				<?php } ?>

			</ul>
		</nav>

		<div class="title-text">
			<h1>GUILD WARS 2 ALTS</h1>
			<h2>Because alts need love too</h2>
			<p class="br">br</p>
			<a href='#' id='TipsBox' class='toggle'>Show</a>
		</div>

	</div>
</header>

<div class='ShowBox' id='ShowBox_TipsBox'>
	 <blockquote>
	In role-playing games, an alternate character, often referred to in slang as alt, alt char, or less commonly multi, is a character in addition to one's "primary" or "Main" player character.
	<cite>Wikipedia</cite>
	</blockquote>
 </div>

	<main>
		<div class="container">
		
		<!-- Content -->
		<?php 
		//-----------------------
		// CONTENT
		//-----------------------
		$page = @$_GET['p'];
		$ext = '.php';
		if( file_exists( $page.$ext ) )
			include ( $page.$ext );
		else
			include ( "home.php" );
		?>


				
		    Q count: 	<?=$db->qCount; ?><hr>
		    Affected: 	<?=$db->affected;?><hr>
		    Last error: <?=$db->lastError; ?><hr>
		    iRecords:	<?=$db->records;?><hr> 
			ArrayedResult:		<?php //echo print_r2($db->arrayedResult);?><hr> 
			Raw Result:	<?php echo print_r2($db->rawResults);?><hr>
			Result:	    <?php echo print_r2($db->result);?><hr>
			Queries:	<?php echo $db->lastQuery;?>
			<!-- content end -->
		</div>
		<p class="br">br</p>
		<p class="br">br</p>
	</main>

	<!--section id="pre-footer">
		<div class="container">
			This is a section
			<br>
			test
		</div>
	</section-->
	<!-- footer --> 
	<footer>
		<div id="footer-content" class="section group">
			<div class="col span_1_of_3">
				Col 1
			</div>
			<div class="col span_1_of_3">
			col 2
			</div>	
			<div class="col span_1_of_3">
				<div class="section group">
					<div class="col span_1_of_3">
						<div class="gw2-logo"> </div>
						<div class="anet-logo"> </div>
						<div class="ncsoft-logo"> </div>
						
					</div>
					<div class="col span_2_of_3">
	    				©2010–2014 ArenaNet, LLC. All rights reserved. Guild Wars, Guild Wars 2, ArenaNet, NCSOFT, the Interlocking NC Logo, and all associated logos and designs are trademarks or registered trademarks of NCSOFT Corporation. All other trademarks are the property of their respective owners.
					</div>
				</div>
			</div>
		</div>
		<div id="footer-after">&copy; Copyright 2058, Example Corporation</div>
	</footer>
	<!-- footer --> 

</div> <!-- wrapper ends -->

</body>
</html>
</html>