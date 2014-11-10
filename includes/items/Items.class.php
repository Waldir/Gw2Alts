<?php

class Items
{
    public $page;
    public $url     = array();
    public $sort;
    public $order = null;
    public $type;
    public $rarity;
    public $search;

    public $limit   = 50;
    public $startPoint;
    public $totalItems;
    private $sharedQuery;
    private $where = array();
    private $like;
    
    public function __construct()
    {
        // set the values.
        $this->setSort();
        $this->setType();
        $this->setRarity();
        $this->setSearch();
        $this->setpage();

        // set the starting point of the query
        $this->startPoint = ( $this->page * $this->limit ) - $this->limit;

        $this->setTotalItems();
    }

    public function setTotalItems()
    {
        global $db;                    
        if ( $count = $db->countRows( 'items', $this->where, 'count(*)', '', '', $this->like ) )
            $this->totalItems = $count;
        else 
            $this->totalItems = 0;
    }

    /*
     * Set the sort array depending on the url 
     */
    private function setSort()
    {
        // Set the Sort
        if( !empty( $_GET['sort'] )           && 
                  ( $_GET['sort'] == 'rarity' || 
                    $_GET['sort'] == 'type'   || 
                    $_GET['sort'] == 'name'   || 
                    $_GET['sort'] == 'level') )
        {
            $this->sort = $_GET['sort'];
            $this->url['sort'] = $_GET['sort'];

            // Set the asc or desc order
            if( !empty( $_GET['order'] ) && 
                      ( $_GET['order'] == 'asc' || 
                        $_GET['order'] == 'desc' ) )
            {
                $this->order = ' ' . $_GET['order'];
                $this->url['order'] = ( $this->order == 'asc' ) ? 'desc' : 'asc';
            } 

        }
    }

    /*
     * Set the type array depending on the url 
     */
    private function setType()
    {
        // Set the Type
        if( !empty( $_GET['type'] ) &&  
        is_numeric( $_GET['type'] ) )
        {
            $this->type          = (int) $_GET['type'];
            $this->where['type'] = (int) $_GET['type'];
            $this->url['type']   = (int) $_GET['type'];
        }
    }

    /*
     * Set the rarity array depending on the url 
     */
    private function setRarity()
    {
        // Set the Rarity
        if( !empty( $_GET['rarity'] ) && 
        is_numeric( $_GET['rarity'] ) )
        {
            $this->rarity          = (int) $_GET['rarity'];
            $this->where['rarity'] = (int) $_GET['rarity'];
            $this->url['rarity']   = (int) $_GET['rarity'];
        } 
    }

    /*
     * Set the search array depending on the url 
     */
    private function setSearch()
    {
        // Set the Search
        if( !empty( $_GET['search'] ) )
        {
            $this->search        = $_GET['search'];
            $this->like['name']  = $this->search;
            $this->url['search'] = $this->search;
        }
    }

     /*
     * Set the page depending on the url 
     */
    private function setPage()
    {
        $this->page        = isset( $_GET['page'] ) ? (int) $_GET['page'] : 1;
        $this->url['p']    = isset( $_GET['p']    ) ? $_GET['p'] : '';
        $this->url['page'] = $this->page;
    }

     /*
     * Return a sort array
     * @return $aSort
     */
    public function getSort()
    {
        return $this->sort;
    }

     /*
     * Return a order array
     * @return $aOrder
     */
    public function getOrder()
    {
        return trim( $this->order );
    }

     /*
     * Return a type array
     * @return $aType
     */
    public function getType()
    {
        return $this->type;
    }

     /*
     * Return a rarity array
     * @return $aRarity
     */
    public function getRarity()
    {
        return $this->rarity;
    }

     /*
     * Return a search array
     * @return $aSearch
     */
    public function getSearch()
    {
        return $this->search;
    }

     /*
     * Return a number of records found
     * @return $totalItems
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

     /*
     * Return a html formatted list of items
     */
    public function getItemsList()
    {
        global $db, $aRarities, $aItem_types;

        // if no records return with nothing
        if( $this->totalItems < 1 )
            return 'No records to display';

        // The item info query
        if( $rows = $db->select( 'items', $this->where, '`item_id`, `name`, `level`, `rarity`, `img_file`, `type`', $this->sort . $this->order, $this->startPoint.' ,' .$this->limit, $this->like ) )
            $records = $db->records;
        else 
            $records = 0;

        // if more than 0 records lets sort them out.
        if( $records === 0 )
            return 'No records to display';

        // Pagination 
        $pagination = tools::pagination( $this->totalItems, $this->limit, $this->page, tools::buildUrl( $this->url, array( 'page' ) ) );
        
        // create a url for the sorting lniks
        $sortUrl = tools::buildUrl( $this->url, array( 'sort', 'order' ));
        // Loop the items found
        $html = $pagination;
        $html .= <<<HTML
            <section class="table">

            <div class="tbl-header">
                <div class="tbl-cell span_5_of_8">
                    <a href="{$sortUrl}sort=name&order=asc"><span class="small_arrow_down"></span></a>
                    Name
                    <a href="{$sortUrl}sort=name&order=desc"><span class="small_arrow_up"></span></a>
                </div>
                <div class="tbl-cell span_1_of_8">
                    <a href="{$sortUrl}sort=level&order=asc"><span class="small_arrow_down"></span></a>
                    level
                    <a href="{$sortUrl}sort=level&order=desc"><span class="small_arrow_up"></span></a>
                </div>
                <div class="tbl-cell span_1_of_8">
                    <a href="{$sortUrl}sort=rarity&order=asc"><span class="small_arrow_down"></span></a>
                    rarity
                    <a href="{$sortUrl}sort=rarity&order=desc"><span class="small_arrow_up"></span></a>
                </div>
                <div class="tbl-cell span_1_of_8">
                    <a href="{$sortUrl}sort=type&order=asc"><span class="small_arrow_down"></span></a>
                    type
                    <a href="{$sortUrl}sort=type&order=desc"><span class="small_arrow_up"></span></a>
                </div>
            </div>
HTML;
        foreach( $rows as $iKey => $sValues )
        {
            if( $sValues['rarity'] == null ) $sValues['rarity'] = 0;

            $tbl_row = tools::cycleCell ( 'tbl-row-light', 'tbl-row-dark' );
            $html .= <<<HTML
            <div class="tbl-row {$tbl_row}">
                <div class="tbl-cell span_5_of_8">
                    <!-- item icon -->
                    <a href="#" class="popper" data-popbox="{$sValues['item_id']}">
                        <img src="{$sValues['img_file']}" class="{$aRarities[$sValues['rarity']]}_b" height="40" width="40">
                    </a>
                
                    <!-- name link -->
                    &nbsp; <a href="?item={$sValues['item_id']}" class="popper {$aRarities[$sValues['rarity']]}" data-popbox="{$sValues['item_id']}">{$sValues['name']}</a>
            
                </div>
                <!-- Hidden -->
                <div id="item_{$sValues['item_id']}" class="popbox"><span class="loading-icon"></span></div>

                <div class="tbl-cell span_1_of_8">{$sValues['level']}</div>
                <div class="tbl-cell span_1_of_8 {$aRarities[$sValues['rarity']]}">{$aRarities[$sValues['rarity']]}</div>
                <div class="tbl-cell span_1_of_8">{$aItem_types[$sValues['type']]}</div>
            </div>
HTML;
        }
        $html .= '</section>';       
        $html .= $pagination;
        return $html; 

    } // end function getItemsList()




} // end class
