<?php
include ('../includes/includes.php');
//echo '<pre>';
//print_r($_POST);
//print_r($_GET);
//print_r($_POST['item_id']);


  /*****************/
 /* Process Class */
/*****************/

// Aurocomplete.
if( !empty( $_REQUEST['term'] ) )
{
	echo tools::autocomplete( $_REQUEST['term'], $_REQUEST['searchFrom'],  $_REQUEST['searchWhat'] );
	return;
}


/* Post:todo detected */
if( !empty( $_POST['todo'] ) )
{
	switch( $_POST['todo'] )
	{
		case 'itemToolTip'  : echo Gw2Api::getItemById( $_POST['item_id'] )->getTooltip(); break; /* Form: Upload image */
		case 'updateDB'     : Tools::UpdateDbFromAPI( empty( $_POST['itemid'] ) ? NULL : $_POST['itemid'] ); break; /* Form: Upload image */
		case 'registerUser' : $user; break; 
		case 'logInUser'    : $user; break; 
		case 'logOutUser'   : $user; break;
		case 'updateProfile': $user; break; 
		case 'createAlt'    : $alts->addAlt(); break;
		case 'updateAlt'    : $alts->updateAlt(); break;  
		default: break;
	}
}