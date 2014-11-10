<?php

class Gw2Alts Extends User
{
    public $totalItems;
    public $limit   = 30;
    public $startPoint;
    public $url     = array();
    public $page;
    public $where;
    public $like;
    public $search;

    private $post_rules;

    public function __construct()
    {
        global $aRace, $aGender, $aProfession, $aLevel;

        // set the rules to sanitize and validate.
        $this->post_rules = array( 
            'character_name' => 'required|alpha_space|min_length:3|max_length:19',
            'character_id'   => 'required|numeric|validate_int',
            'race'           => 'required|numeric|validate_int',  // 'from_array'   => array_keys( $aRace ) ),
            'gender'         => 'required|numeric|validate_int', //'from_array'   => array_keys( $aGender ) ),
            'profession'     => 'required|numeric|validate_int', //'from_array'   => array_keys( $aProfession ) ),
            'level'          => 'required|numeric|validate_int'//'from_array'   => array_keys( $aLevel ) ) );
        );
        // Set the search variables.
        $this->setSearchVars();  
    }

    public function getTotalItems()
    {
        if( empty( $this->totalItems ) )
            $this->setTotalItems();

        return $this->totalItems;
    }

    public function getAltFace( $id )
    {
        return file_exists ( DIR_ALT_FACE.$id.'.jpg' ) ? DIR_ALT_FACE.$id.'.jpg' : DIR_ALT_FACE.'no-face.jpg';
    }

     /**
    * getAltInfo()
    * Get alt info by id or name
    * @return array with alt information
    */   
    public function getAltInfo( $nameOrId, $loggedInUser = false )
    {
        global $db;

        // If nothing was provided return false
        if( empty( $nameOrId ) )
            return false;

        // Create a new empty array
        $where = array();

        // if its numeric we are looking for an id.
        if( is_numeric( $nameOrId ) )
        {
            settype( $nameOrId, "int");
            $where['id'] = $nameOrId;
        }

        // if its a string we are looking for a name
        if( is_string( $nameOrId ) )
        {
            settype( $nameOrId, "string");
            $where['name'] = $nameOrId;
        }

        // if up to this point $where is still empty exit
        if( empty( $where ) )
            return false;

        // if we are pulling data that belongs to the looged in user add his id to the query
        if( $loggedInUser === true )
            if( !empty( $_SESSION['user_id'] ) )
                $where['user_id'] = $_SESSION['user_id'];
            else
                return false;

        // if we get a result return it.
        if( $result = $db->select( 'alts', $where ) )
            return $result;

        // got this far with nothing returned so exit.
        return false;
    }

    /**
    * addAlt()
    * Attempt to create an alt character using the posted information
    * @return a msg of succes or error.
    */
    public function addAlt()
    {
        global $db;
        
        // if user is not logged in kill.
        if( !$this->isLoggedIn() )
            return false;

        // ok lets try this.
        try
        {
            // if the request is not post exit.
            if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
                throw new Exception( "Invalid form submission" );

            // form wasnt submited why are u here?
            if ( empty( $_POST['createAlt'] ) )
                throw new Exception( "Invalid form submission" );

            // Initiate a new validator using the post rules.
            $validator = new Validate( $_POST, $this->post_rules );

            // if validation passed return clean data or error.
            if( $validator->validate() )
                $vars = $validator->getFields();
            else 
                throw new Exception( $validator->getError() );

             // check if the name already in use
             if( $db->countRows( 'alts', array( 'name' =>  $vars['character_name'] ) ) > 0 )
                throw new Exception( 'Character name: already taken.' );

             // everything is ok lets insert this dude
             // vars to insert
             $aVars = array( 'user_id'    => $_SESSION['user_id'], 
                             'name'       => $vars['character_name'], 
                             'race'       => $vars['race'],
                             'gender'     => $vars['gender'],
                             'profession' => $vars['profession'],
                             'level'      => $vars['level'] );
             
            if ( $db->Insert( 'alts', $aVars ) )
                return Tools::returnMsg( "{$aVars['name']} has been created", 1, true, 'Alts-Menu' );
            else
                return Tools::returnMsg( "Couldn not create character", 0 );

        } catch ( Exception $e ) { return Tools::returnMsg( $e->getMessage(), 0, false, false, 'character_name' ); } // end try/catch

        // if we got this far there is a problem.
        return Tools::returnMsg( 'There was a problem' );

    } // end function addAlt()


    public function updateAlt()
    {
        global $db;
        
        // if user is not logged in kill.
        if( !$this->isLoggedIn() )
            return false;

        // ok lets try this.
        try
        {
            // if the request is not post exit.
            if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
                throw new Exception( "Invalid form submission" );

            // form wasnt submited why are u here?
            if ( empty( $_POST['updateAlt'] ) )
                throw new Exception( "Invalid form submission" );

            // Initiate a new validator using the post rules.
            $validator = new Validate( $_POST, $this->post_rules );

            // if validation passed return clean data or error.
            if( $validator->validate() )
                $vars = $validator->getFields();
            else {
                $erro_field = $validator->getErrorField();
                throw new Exception( $validator->getError() );
            }
             // check if the name already in use
             if( $result = $db->executeSQL( "SELECT count(*) as c FROM `alts` WHERE `name` = '{$vars['character_name']}' AND `id` <> '{$vars['character_id']}';" ) )
                if( $result['c'] > 0 )
                {
                    $erro_field = 'character_name';
                    throw new Exception( 'Name: already taken.' );
                }

             // everything is ok lets insert this dude
             // vars to insert
             $aVars = array( 'name'   => $vars['character_name'],
                             'gender' => $vars['gender'],
                             'level'  => $vars['level'] );
             
            if ( $db->update( 'alts', $aVars, array( 'id' => $vars['character_id'], 'user_id' => $_SESSION['user_id'] ) ) )
                return Tools::returnMsg( "{$aVars['name']} has been updated", 1 );
            else
                return Tools::returnMsg( "Could not update character".$db->lastQuery. $db->lastError, 0 );

        } catch ( Exception $e ) { return Tools::returnMsg( $e->getMessage(),  0, false, false,  $erro_field ); } // end try/catch

        // if we got this far there is a problem.
        return Tools::returnMsg( 'There was a problem' );

    } // end function

    /*
     * Returns a html formatted list of the user's alts
     */
    public function getUserAlts( $id )
    {
        global $db, $aRace, $aGender, $aProfession;

        // pull the alts
        if( !$result = $db->Select( 'alts', array( 'user_id' => $id ), '`id`, `name`, `race`, `gender`, `profession`, `level`' ) )
            return false;

        // No records found!
        if( $db->records < 1 ) 
            return false;

        $sAltsHTML = "";

        foreach( $result as $ikey => $alt )
        {
            $id      = $alt['id'];
            $name    = $alt['name'];
            $race    = $aRace[$alt['race']];
            $gen     = $aGender[$alt['gender']];
            $prof    = $aProfession[$alt['profession']];
            $lvl     = $alt['level'];
            $face    = $this->getAltFace( $id );

            $sAltsHTML .= <<<HTML
            <div class='alt-box'>
                <div class='alt-info tbl-header'><h2>{$name}</h2></div>
                <div class='alt-face-container' >
                    <img src='{$face}' class='alt-face {$prof}_b'>
                    <p class='lvl'>{$lvl}</p>
                    <p class='{$prof}-icon icon'>Profession Icon</p>
                </div>
                <div class='alt-info'>
                    <p>{$gen} {$race} {$prof}</p>
                    <p> <a href=''>View</a> | <a href='?p=my-alts&edit={$name}'>Edit</a> | <a href=''>Delete</a> | <a href=''>Share</a></p>
                </div>
            </div>
HTML;
        }
        $sAltsHTML .= "";

        return $sAltsHTML;

    } // end function

     /*
     * Set the page depending on the url 
     */
    private function setSearchVars()
    {
        $this->page = isset( $_GET['page'] ) ? (int)$_GET['page'] : 1;
        $this->url['p'] = isset( $_GET['p'] ) ? $_GET['p'] : '';
        $this->url['page'] = $this->page;

        if( !empty( $_GET['search'] ))
        {
            $this->search       = $_GET['search'];
            $this->like['name'] = $_GET['search'];
        }

        // set the starting point of the query
        $this->startPoint = ( $this->page * $this->limit ) - $this->limit;
    }

    public function getSearch()
    {
        return $this->search;
    }

    public function getAllAlts()
    {
        global $db, $aRace, $aGender, $aProfession;

        // set the total of items
        $this->setTotalItems();

        // if there is no items terminate here.
        if( $this->totalItems < 1 )
            return 'Error: No items to display.';

        // The item info query
        if( $rows = $db->select( 'alts', $this->where, '`id`, `user_id`, `name`, `race`, `gender`, `profession`, `level`', '', $this->startPoint.' ,' .$this->limit, $this->like ) )
            $records = $db->records;
        else 
            $records = 0;

        // if more than 0 records lets sort them out.
        if( $records === 0 )
            return 'No records to display';

        // Pagination 
        $pagination = tools::pagination( $this->totalItems, $this->limit, $this->page, tools::buildUrl( $this->url, array( 'page' ) ) );

        $html = '';
        $html .= $pagination;
        $html .= <<<HTML
            <section class="table">

            <div class="tbl-header">
                <div class="tbl-cell span_4_of_8"> Name </div>
                <div class="tbl-cell span_1_of_8"> Level  </div>
                <div class="tbl-cell span_1_of_8"> Race  </div>
                <div class="tbl-cell span_1_of_8"> Gender </div>
                <div class="tbl-cell span_1_of_8"> Profession </div>
            </div>
HTML;
        foreach( $rows as $iKey => $sValues )
        {
            $tbl_row = tools::cycleCell ( 'tbl-row-light', 'tbl-row-dark' );
            $face    = $this->getAltFace( $sValues['id'] );
            $html .= <<<HTML
            <div class="tbl-row {$tbl_row}">
                <div class="tbl-cell span_4_of_8">
                    <!-- item icon -->
                    <a href="#" class="popper">
                        <img src="{$face}" class="{$aProfession[$sValues['profession']]}_b" height="40" width="40">
                    </a>
                
                    <!-- name link -->
                    &nbsp; <a href="?alt={$sValues['id']}">{$sValues['name']}</a>
            
                </div>

                <div class="tbl-cell span_1_of_8">{$sValues['level']}</div>
                <div class="tbl-cell span_1_of_8">{$aRace[$sValues['race']]}</div>
                <div class="tbl-cell span_1_of_8">{$aGender[$sValues['gender']]}</div>
                <div class="tbl-cell span_1_of_8">{$aProfession[$sValues['profession']]}</div>
            </div>
HTML;
        }

        $html .= '</section>';
        $html .= $pagination;
        return $html;
    } // end function

        public function setTotalItems()
        {
            global $db;
            if ( $count = $db->countRows( 'alts', $this->where, 'count(*)', '', '', $this->like ) )
                $this->totalItems = $count;
            else 
                $this->totalItems = 0;
        }

} // class end
?>