<?php
class Tools 
{


    public static function buildUrl( $array = array(), $exclude = array() )
    {
        $url = $array;

        if( empty( $exclude ) )
            return http_build_query( $url );

        foreach( $exclude as $sE )
            unset( $url[$sE] );

        return '?' . http_build_query( $url ) . '&';
    }

  /* 
   * This function updates the database using the the Gw2 API
   */
  public static function UpdateDbFromAPI( $itemsId ) 
  {
    global $db;
    // Set the time out to unlimited cause its gonna be a long loop.
    set_time_limit(0);
    // Ignore if the user stops the script
    ignore_user_abort(true);
    // This part makes a desition wather to 
    // update the entire db or just a single item
    if( !empty( $itemsId ) )
    {
      // Update a submited item(s) Multiple items are comma separated.
      $itemsId = preg_replace('/\s/', '', $itemsId); // remove whitespaces (1, 2, 3, => 1,2,3)
      $itemsId = rtrim( $itemsId, ',' );            // Remove extra commas.
      $aItemsId = array( 'items' => explode( ',', $itemsId ) ); 
    } else 
      $aItemsId = Gw2Api::getItemsFromApi( ); // Update the entire db

    // Number of items
    $number_of_items = count( $aItemsId );
    
    // Counters
    $i = 0;
    $ii = 0;
    $sMsg = '';

    /*/ Sql query (Insert one item or all)
    $sql = 'INSERT IGNORE INTO items ( item_id ) VALUES ('. implode( '),(', $aItemsId ) . ')' ;
    
    if( ! MySql::ExecuteSQL( $sql, true ) ) 
      return Tools::returnMsg( 'Item(s) could not be inserted.<pre>'.MySQL::$sLastError.'</pre>' ); // Items could not be inserted.
    */
  
    foreach( array_chunk( $aItemsId, 1000 ) as $items ) 
    {
      $i++;
      foreach ( $items as $item_id ) 
      {
        $ii++;
        echo "[{$ii} / {$number_of_items}]: (". floor( ($i  / $number_of_items ) * 100 ) ."%)";

        try 
        { 
          $APIItem = Gw2Api::getItemById( $item_id );
          
          if ( $APIItem === null ) 
            throw new Exception( "Item #{$item_id} not found" );
          
          // set all the common properties
          $itemData = array( 
            'item_id'                  => $APIItem->getItemId( ),
            'name'                     => $APIItem->getName( ), 
            'description'              => $APIItem->getDescription(),
            'type'                     => $APIItem->getTypeSubmit(),
            'level'                    => $APIItem->getLevel(),
            'rarity'                   => $APIItem->getRarityInsert(),
            'vendor_value'             => $APIItem->getVendorValue(),
            'img_file'                 => $APIItem->getImgFIle(),
            'game_types'               => $APIItem->getGameTypes(),
            'flags'                    => $APIItem->getFlags(),
            'restrictions'             => $APIItem->getRestrictions(),
                     
            'infusion_slots'           => $APIItem->getInfusionSlotsInsert(),
            'default_skin'             => $APIItem->getDefaultSkin(),
            'infix_upgrade'            => $APIItem->getAttributesInsert(),
            'suffix_item_id'           => $APIItem->getSuffixId(),
            'secondary_suffix_item_id' => $APIItem->getSecondSuffixItem()
          );
          
          // if the Method exists
          if( method_exists( $APIItem, 'getSubTypeInsert' ) ) $itemData['sub_type']     = $APIItem->getSubTypeInsert();
          if( method_exists( $APIItem, 'getDefense' ) )       $itemData['defense']      = $APIItem->getDefense();
          if( method_exists( $APIItem, 'getDamageType' ) )    $itemData['damage_type']  = $APIItem->getDamageType();
          if( method_exists( $APIItem, 'getMinPower' ) )      $itemData['min_power']    = $APIItem->getMinPower();
          if( method_exists( $APIItem, 'getMaxPower' ) )      $itemData['max_power']    = $APIItem->getMaxPower();
          if( method_exists( $APIItem, 'getWeightClass' ) )   $itemData['weight_class'] = $APIItem->getWeightClass();

          // Secure the Data
          $itemData = $db->SecureData( $itemData );

          // print_r2($itemData); // Print the clean array for testing
          // Create the table values and colums
          $sInsert = '';
          $aInsert = array();
          foreach( $itemData as $iKey_insert => $sValue_insert )
          {
            if( !empty( $sValue_insert ) ){
              $sInsert .= "`{$iKey_insert}` = '{$sValue_insert}',". PHP_EOL ;
              $aInsert[$iKey_insert] = $sValue_insert; 
            }
          }
          // remove the last comma and whipespace
          $sInsert = substr( $sInsert, 0, -3 );

          // Build the query
          $sql = "
          INSERT iNTO `items` 
            ( `".implode( '`,`', array_keys( $aInsert ) )."` ) 
          VALUES 
            ( '".implode( "','", $aInsert )."' )
          ON DUPLICATE KEY UPDATE 
          {$sInsert}";

          // Exception of the query 
          if( $db->ExecuteSQL( $sql, 1 ) )
            echo  "Success: Item #{$itemData['item_id']} added.<br />";
          else
            echo  "Error: Item #{$itemData['item_id']} failed.<br />" .$db->sLastError;

        } catch (Exception $e) {
           echo 'Error:' . $e->getMessage() . '<br />';
        } // end try/catch
        // flush out some input
        //echo Tools::returnMsg($sMsg);
        ob_flush();
        flush();
      } // end loop 2
      // Sleep after every chunk inserted
      sleep(2);

    } // end loop 1
    return Tools::returnMsg( $sMsg );
  } // end function

  
  /*
    autocomplete() uses jqueary to autocomplete
    forms dynamically. term is gathered by 
    ?term=$term.
    Returns a json array.
  */
  static public function autocomplete( $term, $table, $colum )
  {
    global $db;
    $term = $db->SecureData( $term );
    $sql = "SELECT $colum
        FROM `$table`
        WHERE `$colum` LIKE '%{$term}%'";
         
    $db->ExecuteSQL( $sql , true);
    if( $db->iRecords > 0 )
    {
      if( $db->iRecords == 1 )
        $rows = array( $db->aArrayedResult );
      else
        $rows = $db->aArrayedResults;
        
      // Loop the items found
      foreach( $rows as $iKey => $sValues )
        $result[] = array( 'label' => $sValues['name'] );
      return json_encode( $result );
    }
  }
  
   /**********************************************\
   * Return Message 
   * $msg: the message
   * $success: Error or succes, success by default
     * $redirect: true redirects to refferer
     * $remove: removes an id element
   \**********************************************/

   static public function returnMsg ( $msg, $success = 1,  $redirect = false, $remove = false, $append = false ) 
   {
    if( $redirect )
      $new_msg = '
      <noscript>
      <meta http-equiv="refresh" content="5;url='.$_SERVER['HTTP_REFERER'].'" />
      </noscript>
      <p>'.$msg.'</p>
      <p>redirecting in <span id="seconds">5</span></p>
      <script>
        var seconds = 5;
        setInterval(
        function()
        { 
          if (seconds <= 1)
          {
            window.location = "'.$_SERVER['HTTP_REFERER'].'";
          } else {
              document.getElementById("seconds").innerHTML = --seconds;
          }
        }, 1000 );
        </script>';
    else
      $new_msg = $msg;
      
    $value  = array( 'error_success' => $success,
                     'msg'           => $new_msg,
                     'remove'        => $remove,
                     'append'        => $append );
       
    if ( empty( $_POST['ajaxrequest'] ) )
      echo $new_msg;
    else 
    {
      header('Content-Type: application/json');
      echo json_encode( $value );
    }
  }
  //-----------------------
  // CONTENT
  //-----------------------
  static public function content ( $page, $ext = '.php' )
  {
    if( file_exists( $page.$ext ) )
      include ( $page.$ext );
    else
      include ( "home.php" );
  }
  
  //-----------------------
  // PAGE NAVIGATION
  //-----------------------
   public static function pagination( $total, $per_page = 10, $page = 1, $url = '?' )
   {
      $adjacents = 2; 

      $page  = ($page == 0 ? 1 : $page);  
      $start = ($page - 1) * $per_page;               
    
      $prev = $page - 1;              
      $next = $page + 1;
        $lastpage = ceil( $total/$per_page );
      $lpm1 = $lastpage - 1;
      $pagination = "";
      if( $lastpage > 1 )
      {
        $pagination .= "<hr>";
        $pagination .= "<div class='Pagination'>";
      $pagination .= "<span class='noneLink toHide'>Page $page of $lastpage</span>";
      
      if ( $page == 1)
        $pagination.= ''; // <-- Remove if below line is enabled.
                //$pagination.= "<span class='noneLink'>&laquo;</span>";
      else
        $pagination.= "<span><a href='{$url}page=$prev'>&laquo;</a></span>";
      
        if ( $lastpage < 7 + ( $adjacents * 2) )
        { 
          for ($i = 1; $i <= $lastpage; $i++)
          {
            if ( $i == $page )
              $pagination.= "<span class='noneLink toHide'>{$i}</span>";
            else
              $pagination.= "<span class='toHide'><a href='{$url}page=$i'>{$i}</a></span>";          
          }
        }
        elseif($lastpage > 5 + ($adjacents * 2))
        {
          if($page < 1 + ($adjacents * 2))    
          {
            for ($i = 1; $i < 4 + ($adjacents * 2); $i++)
            {
              if ( $i == $page )
                $pagination.= "<span class='noneLink'>{$i}</span>";
              else
                $pagination.= "<span><a href='{$url}page={$i}'>{$i}</a></span>";            
            }
            $pagination.= "<span class='dot'>...</span>";
            $pagination.= "<span class='toHide'><a href='{$url}page=$lpm1'>$lpm1</a></span>";
            $pagination.= "<span class='toHide'><a href='{$url}page=$lastpage'>$lastpage</a></span>";  
          }
          elseif( $lastpage - ( $adjacents * 2 ) > $page && $page > ( $adjacents * 2 ) )
          {
            $pagination.= "<span><a href='{$url}page=1'>1</a></span>";
            $pagination.= "<span><a href='{$url}page=2'>2</a></span>";
            $pagination.= "<span class='dot'>...</span>";
            for ($i = $page - $adjacents; $i <= $page + $adjacents; $i++)
            {
              if ( $i == $page )
                $pagination.= "<span class='noneLink'>{$i}</span>";
              else
                $pagination.= "<span><a href='{$url}page=$i'>{$i}</a></span>";          
            }
            $pagination.= "<span class='dot'>...</span>";
            $pagination.= "<span><a href='{$url}page=$lpm1'>$lpm1</a></span>";
            // $pagination.= "<span><a href='{$url}page=$lastpage'>$lastpage</a></span>";   
          }
          else
          {
            $pagination.= "<span><a href='{$url}page=1'>1</a></span>";
            $pagination.= "<span><a href='{$url}page=2'>2</a></span>";
            $pagination.= "<span class='dot'>...</span>";
            for ($i = $lastpage - (2 + ($adjacents * 2)); $i <= $lastpage; $i++)
            {
              if ($i == $page)
                $pagination.= "<span class='noneLink'>{$i}</span>";
              else
                $pagination.= "<span><a href='{$url}page=$i'>{$i}</a></span>";          
            }
          }
        }
        
        if ( $page < $i - 1){ 
          $pagination.= "<span><a href='{$url}page=$next'>&raquo;</a></span>";
                //$pagination.= "<span><a href='{$url}page=$lastpage'>Last</a></span>";
        }else{
          //$pagination.= "<span class='noneLink'>&raquo;</span>";
                //$pagination.= "<span class='noneLink'>Last</span>";
            }
        $pagination.= "</div>\n";   
      }
    
    
        return $pagination;
    } // End Function 

  /* Cycle cell color or class */
  static public function cycleCell ( $aColor, $bColor ) 
  {
    static $bgc = TRUE;
    return ( ( $bgc = !$bgc ) ? $bColor : $aColor );
  }
  
  static public function dropMenu ( $aArray, $name = 'name', $selected = NULL )
  {
    if( is_array( $aArray ) && count( $aArray ) > 0 )
    {
      $nameUp = ucfirst( $name );
      $dropMenu  = "<select name='{$name}'>". PHP_EOL;
      $dropMenu .= "<option value=''>{$nameUp}</option>". PHP_EOL;
      $dropMenu .= "<optgroup label='{$nameUp}'>";

      foreach( $aArray as $iKey => $sValue  ) 
      {
        $selectedVal = ( $selected == $iKey ) ? ' selected' : '';
        $dropMenu .= "<option value='{$iKey}' class='{$sValue}'{$selectedVal}>{$sValue}</option>". PHP_EOL;
      }
      $dropMenu .= "</select>". PHP_EOL;
      return $dropMenu;
    } else {
      return false;
    }
  }
  
  static public function simpleDropMenu( $array, $name = 'name', $selected = null )
  {
    if( is_array( $array ) && count( $array ) > 0 )
    {
      $nameUp    = ucfirst( $name );
      $dropMenu  = "<select name='{$name}'>". PHP_EOL;
      $dropMenu .= "<optgroup label='{$nameUp}'>";

      foreach( $array as $iKey => $sValue  ) 
      {
        $selectedVal = ( $selected == $iKey ) ? ' selected' : '';
        $dropMenu .= "<option value='{$iKey}'{$selectedVal}>{$sValue}</option>". PHP_EOL;
      }
      $dropMenu .= "</select>". PHP_EOL;
      return $dropMenu;
    } else {
      return false;
    }

  }
  /* Returns a char code from the id */
  function getChatCodeFromId( $id )
  {
    return '[&' . base64_encode(chr(0x02) . chr(0x01) . chr($id % 256) . chr((int)($id / 256)) . chr(0x00) . chr(0x00)) . ']';
  }

  public static function getGw2Money ( $copper )
  {
    $goldImg   = '<span class="Gw2Money-Gold">g</span>';
        $silverImg = '<span class="Gw2Money-Silver">s</span>';
        $copperImg = '<span class="Gw2Money-Copper">c</span>';

        $copper = intval( $copper );

        if ($isNegative = $copper < 0) {
             $copper *= -1;
        }

        $result = "";

        if ($gold = floor($copper / 10000)) {
            $copper = $copper % ($gold * 10000);

            $result .= "<span class='Money-Gold'>{$gold}</span> {$goldImg}&nbsp;";
        }

        if ($silver = floor($copper / 100)) {
            $copper = $copper % ($silver * 100);

            $result .= "<span class='Money-Silver'>{$silver}</span> {$silverImg}&nbsp;";
        }

        if ($copper) {
            $result .= "<span class='Money-Copper'>{$copper}</span> {$copperImg}";
        }

        if ($isNegative) {
            $result = $result;
        }

        return $result ? trim( $result ) : "";

  }

  public static function percent( $num_amount, $num_total ) {
    $count1 = $num_amount / $num_total;
    $count2 = $count1 * 100;
    $count = number_format($count2, 0);
    echo $count;
  }
  
  
} // End class
?>