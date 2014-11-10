<?php
    spl_autoload_register( function( $ClassName ) 
    {
		$incDir = dirname(__FILE__);
        //class directories
        $directorys = array(
            $incDir.'/GW2API/',
            $incDir.'/',
            $incDir.'/members/',
            $incDir.'/alts/',
            $incDir.'/items/'
        );
        
        //for each directory
        foreach($directorys as $directory)
        {
            //see if the file exsists
            if( is_readable( $directory.$ClassName . '.class.php' ) )
            {
                require( $directory.$ClassName . '.class.php' );
                return;
            }            
        }
    });
?>