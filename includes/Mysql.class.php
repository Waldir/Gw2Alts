<?php
/*
 * mysql.class.php
 * Modefied version of:
 * https://github.com/a1phanumeric/PHP-MySQL-Class/blob/master/class.MySQL.php
*/

// MySQL Class
class MySQL {
	// Base variables

	public $lastError;		// Holds the last error
	public $lastQuery;		// Holds the last query
	public $result;			// Holds the MySQL query result
	public $records;		// Holds the total number of records returned
	public $affected;		// Holds the total number of records affected
	public $rawResults;		// Holds raw 'arrayed' results
	public $arrayedResult;	// Holds a single 'arrayed' result
	public $qCount = 0;			// Ammount of queries made.
	
	private $hostname;		// MySQL Hostname
	private $username;		// MySQL Username
	private $password;		// MySQL Password
	private $database;		// MySQL Database
	private $link;			// Database Connection Link
	
	/* *******************
	 * Class Constructor *
	 * *******************/
	function __construct($database, $username, $password, $hostname='localhost', $port=3306){
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		$this->hostname = $hostname.':'.$port;
		
		$this->Connect();
	}
	
	// Connects class to database
	// $bPersistant (boolean) - Use persistant connection?
	private function Connect( )
	{
		$this->CloseConnection();
		
		$this->link = new mysqli( $this->hostname, $this->username, $this->password );

		if ( mysqli_connect_errno() )
		{
   			$this->lastError .= 'Could not connect to server: ' . mysqli_connect_error( );
			return false;
		}
		
		if( !$this->UseDB( ) )
		{
			$this->lastError .= 'Could not connect to database: ' . mysqli_error( $this->link );
			return false;
		}
		return true;
	}

    // Closes the connections
    public function closeConnection()
    {
        if( $this->link)
            mysqli_close($this->link);
    }
	
	// Select database to use
	private function UseDB()
	{
		if ( !mysqli_select_db( $this->link, $this->database ) )
		{
			$this->lastError .='Cannot select database: ' . mysqli_error( $this->link );
			return false;
		} else {
			return true;
		}
	}

	// Performs a 'mysqli_real_escape_string' on the entire array/string
	public function SecureData( $data )
	{
		if( is_array( $data ) )
		{
            $i = 0;
			foreach( $data as $key=>$val )
			{
				if( !is_array( $data[$key] ) )
				{
					$data[$key] = mysqli_real_escape_string( $this->link, $data[$key] );
                    $i++;
				}
			}
		} else {
			$data = mysqli_real_escape_string( $this->link, $data);
		}
		return $data;
	}


    // Executes MySQL query
    public function executeSQL( $q )
    {
        $this->lastQuery .= '<pre>('.$this->qCount.') '.$q.'</pre>';
        if( $this->result = mysqli_query( $this->link, $q ) )
        {
            if ( gettype( $this->result ) === 'object' ) 
            {
                $this->records  = @mysqli_num_rows( $this->result );
                $this->affected = @mysqli_affected_rows( $this->link );
                ++$this->qCount;
            } else {
               $this->records  = 0;
               $this->affected = 0;
            }

            if( $this->records > 0 )
            {
                $this->arrayResults();
                return $this->arrayedResult;
            } else {
                return true;
            }

        } else {
            $this->lastError .= mysqli_error( $this->link );
            return false;
        }
    }
	
	// Adds a record to the database
	// based on the array key names
	public function insert( $table, $vars , $exclude = '' )
	{
		// Catch Exceptions
		if($exclude == '') 
			$exclude = array();
		
		array_push($exclude, 'MAX_FILE_SIZE'); // Automatically exclude this one
		
		// Prepare Variables
		 $vars = $this->SecureData( $vars );
		
        $q = "INSERT INTO `{$table}` SET ";
        foreach( $vars as $key=>$value )
        {
            if( in_array( $key, $exclude ) )
                continue;

            $q .= "`{$key}` = '{$value}', ";
        }

        $q = trim( $q, ', ' );

        return $this->executeSQL( $q );
    }
	
    // Deletes a record from the database
    public function delete( $table, $where='', $limit='', $like=false )
    {
        $q = "DELETE FROM `{$table}` WHERE ";
        if( is_array( $where ) && $where != '' )
        {
            // Prepare Variables
            $where = $this->SecureData( $where );

            foreach( $where as $key=>$value )
            {
                if( $like )
                    $q .= "`{$key}` LIKE '%{$value}%' AND ";
                else
                    $q .= "`{$key}` = '{$value}' AND ";
            }

            $q = substr( $q, 0, -5 );
        }

        if( $limit != '' )
            $q .= ' LIMIT ' . $limit;

        return $this->executeSQL( $q );
    }

    // Gets a single row from $from where $where is true
    public function select( $from, $where='', $cols='*', $orderBy='', $limit='', $like='', $operand='AND' )
    {
        // Catch Exceptions
        if( trim( $from ) == '' )
            return false;

        $q = "SELECT {$cols} FROM `{$from}`";

        if( is_array( $where ) && $where != '' && !empty( $where ) )
        {
        	$setClause = true;
        	$q .= " WHERE ";	
            // Prepare Variables
            $where = $this->SecureData( $where );

            // loop where
            foreach( $where as $key=>$value )
				$q .= "`{$key}` = '{$value}' {$operand} ";

            $q = substr( $q, 0, -( strlen( $operand )+2 ) );
        } else {
        	$setClause = false;
        }

        // LIKE
        if( is_array( $like ) && $like != '' && !empty( $like ) )
        {
        	$q .= $setClause ? " $operand " : " WHERE ";
        	$like = $this->SecureData( $like );

        	foreach( $like as $key=>$value )
        		$q .= "`{$key}` LIKE '%{$value}%' {$operand} ";

        	$q = substr( $q, 0, -( strlen( $operand )+2 ) );
        }

        if( $orderBy != '' ) $q .= ' ORDER BY ' . $orderBy;
        if( $limit   != '' ) $q .= ' LIMIT ' . $limit;
        
        return $this->executeSQL( $q );
    }
	
    // Updates a record in the database based on WHERE
    public function update( $table, $set, $where, $exclude = '' )
    {
        // Catch Exceptions
        if(trim( $table ) == '' || !is_array( $set ) || !is_array( $where ) )
            return false;

        if( $exclude == '' )
            $exclude = array();
        
        array_push( $exclude, 'MAX_FILE_SIZE' ); // Automatically exclude this one

        $set 	= $this->SecureData( $set );
        $where 	= $this->SecureData( $where );

        // SET

        $q = "UPDATE `{$table}` SET ";

        foreach( $set as $key=>$value )
        {
            if( in_array( $key, $exclude ) )
                continue;

            $q .= "`{$key}` = '{$value}', ";
        }

        $q = substr( $q, 0, -2 );

        // WHERE

        $q .= ' WHERE ';

        foreach( $where as $key=>$value )
            $q .= "`{$key}` = '{$value}' AND ";

        $q = substr( $q, 0, -5 );

        return $this->executeSQL( $q );
    }
	
	// 'Arrays' a single result
	private function arrayResult()
	{
		$this->arrayedResult  = mysqli_fetch_assoc( $this->result ) or die ( mysqli_error( $this->link ) );
		return $this->arrayedResult;
	}

    // 'Arrays' multiple result
    public function arrayResults()
    {
        if($this->records == 1)
            return $this->arrayResult();

        $this->arrayedResult = array();
        
        while ( $data = mysqli_fetch_assoc( $this->result ) )
            $this->arrayedResult[] = $data;

        return $this->arrayedResult;
    }
	
    // 'Arrays' multiple results with a key
    public function arrayResultsWithKey( $key='id' )
    {
        if( isset( $this->arrayedResult ) )
            unset($this->arrayedResult);

        $this->arrayedResult = array();

        while( $row = mysqli_fetch_assoc( $this->result ) )
            foreach( $row as $theKey => $theValue )
                $this->arrayedResult[$row[$key]][$theKey] = $theValue;

        return $this->arrayedResult;
    }
	
    // Returns last insert ID
    public function lastInsertID()
    {
        return mysqli_insert_id();
    }

    // Return number of rows
    public function countRows( $from, $where='', $cols = 'count(*)', $orderBy='', $limit='', $like='', $operand='AND'  )
    {
        $result = $this->select( $from, $where, $cols, $orderBy, $limit, $like, $operand );
        return $result["count(*)"];
    }

}
?>