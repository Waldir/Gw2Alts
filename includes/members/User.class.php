<?php

class User
{
  private $user_id = null;
  private $user_name = null;
  private $user_email = null;

  /**
  * the function "__construct()" automatically starts whenever an object of this class is created,
  * you know, when you do "$registration = new Registration();"
  */
  public function __construct()
  {
    // create/read session, absolutely necessary
    session_start();

    if ( isset( $_POST['register'] ) )
      $this->registerNewUser();

    // check the possible login actions:
    // if user tried to log out (happen when user clicks logout button)
    if ( isset( $_POST['logout'] ) && $this->isLoggedIn( ) )
      $this->doLogout();

    // login via post data (if user just submitted a login form)
    if ( isset( $_POST['login'] ) )
      $this->dologinWithPostData();

    // Anything in here needs the user to be logged in.
    if( $this->isLoggedIn( ) )
    {
      // update info 
      if( isset( $_POST['update']))
        $this->updateUserInfo();

      $this->user_id    = $_SESSION['user_id'];
      $this->user_name  = $_SESSION['user_name'];
      $this->user_email = $_SESSION['user_email'];

    }

  }

  /**
   * @retuen $user_id;
   */
  public function getUserId()
  {
    return $this->user_id;
  }

   /**
   * @retuen $user_name;
   */
  public function getUserName()
  {
    return $this->user_name;
  }

  /**
   * @retuen $user_email;
   */
  public function getUserEmail()
  {
    return $this->user_email;
  }

  /**
  * handles the entire registration process. checks all error possibilities
  * and creates a new user in the database if everything is fine
  */
  private function registerNewUser()
  {
    global $db;
    try 
    {
      // Check the Email
      if ( empty( $_POST['newEmail'] ) )                                throw new Exception( 'Email: cannot be empty' );
      if ( strlen( $_POST['newEmail'] ) > 64 )                          throw new Exception( 'Email: cannot be longer than 64 characters' );
      if ( !filter_var($_POST['newEmail'], FILTER_VALIDATE_EMAIL ) )    throw new Exception( 'Email: not in a valid email format' );

      // Chcekc the username
      if ( empty( $_POST['newUsername'] ) )                             throw new Exception( 'Username: cannot be empty' );
      if ( strlen( $_POST['newUsername'] ) > 64 || 
       strlen( $_POST['newUsername'] ) < 5 )                            throw new Exception( 'Username: between 5 and 64 characters long' );
      if ( !preg_match( '/^[a-z\d]{2,64}$/i', $_POST['newUsername'] ) ) throw new Exception( 'Username: only a-Z and numbers are allowed' );

      // check the password
      if ( empty( $_POST['newPassword'] ) )                             throw new Exception( 'Password: cannot be empty' );
      if ( empty( $_POST['newPasswordCheck'] ) )                        throw new Exception( 'Re-enter Password: cannot be empty' );
      if ( $_POST['newPassword'] !== $_POST['newPasswordCheck'] )       throw new Exception( 'Password: does not match re-entered password' );
      if ( strlen( $_POST['newPassword'] ) < 6)                         throw new Exception( 'Password: minimum length of 6 characters' );

      // One last check
      if ( !empty( $_POST['newUsername'] )
       && strlen( $_POST['newUsername'] ) <= 64
       && strlen( $_POST['newUsername'] ) >= 5
       && preg_match( '/^[a-z\d]{2,64}$/i', $_POST['newUsername'] )
       && !empty( $_POST['newEmail'] )
       && strlen( $_POST['newEmail'] ) <= 64
       && filter_var( $_POST['newEmail'], FILTER_VALIDATE_EMAIL )
       && !empty( $_POST['newPassword'] )
       && !empty( $_POST['newPasswordCheck'] )
       &&  ($_POST['newPassword'] === $_POST['newPasswordCheck'] ) ) 
      {
       // escaping, additionally removing everything that could be (html/javascript-) code
       $newUsername = strip_tags($_POST['newUsername'], ENT_QUOTES );
       $newEmail    = strip_tags($_POST['newEmail'],    ENT_QUOTES );

       // What to check for in the db
       $aWhere = array( 'user_name'  => $newUsername, 
                        'user_email' => $newEmail );
       
       // check if user or email address already exists
       $db->Select( 'users', $aWhere, '`user_name`, `user_email`', '', '', false, ' OR' );

       // Stop the username or email is in use
       if ( $db->iRecords == 1 )
        throw new Exception( 'Sorry, that username and or email address is already taken.' );

       // vars to insert
       $aVars = array( 'user_name'          => $newUsername, 
                       'user_password_hash' => $this->passHash( $_POST['newPassword'] ), 
                       'user_email'         => $newEmail );
       
       // write new user's data into database
       // if user has been added successfully
       if ( $db->Insert( $aVars, 'users' ) )
       {
                  // write user data into PHP SESSION ( a file on your server )
          $_SESSION['user_name']  = $aVars['user_name'];
          $_SESSION['user_email'] = $aVars['user_email'];
          $_SESSION['user_id']    = $db->iLastInsertId;
          $_SESSION['user_login_status'] = 1;
        return Tools::returnMsg( 'Your account has been created successfully <br> and you are now logged in..', 1, true, 'User-Menu' );
       } else
        throw new Exception( 'Sorry, your registration failed. ' );
      } else {
       throw new Exception( 'Sorry, there was a problem.' );
      }

     } catch ( Exception $e ) {
      return Tools::returnMsg( $e->getMessage(), 0 );
     } // end try/catch

    } // end registerNewUser()

  /**
   * log in with post data
   */
  private function dologinWithPostData()
  {
    global $db;
    try 
    {
      // check login form contents
      if ( empty( $_POST['userName'] ) )       throw new Exception( 'Username: cannot be empty.' );
      if ( empty( $_POST['userPassword'] ) )   throw new Exception( 'Password: cannot be empty.' );

      // one last check
      if ( !empty( $_POST['userName'] ) && !empty( $_POST['userPassword'] ) ) 
      {
        // database query, getting all the info of the selected user (allows login via email address in the
        // username field)

        // What to check for in the db
        $aWhere = array( 'user_name'  => $_POST['userName'], 
                         'user_email' => $_POST['userName'] );

        if( !$result = $db->Select( 'users', $aWhere, '`user_id`, `user_name`, `user_email`, `user_password_hash`', '', '', false, ' OR' ) )
          throw new Exception( 'This user does not exist.');

        // if this user exists
        if ( $db->records < 1 )
          throw new Exception( 'This user does not exist.');

        // using PHP 5.5's password_verify() function to check if the provided password fits
        // the hash of that user's password
        if ( password_verify( $_POST['userPassword'], $result['user_password_hash'] ) ) 
        {
          // write user data into PHP SESSION ( a file on your server )
          $_SESSION['user_name']  = $result['user_name'];
          $_SESSION['user_email'] = $result['user_email'];
          $_SESSION['user_id']    = $result['user_id'];
          $_SESSION['user_login_status'] = 1;
          return Tools::returnMsg( "Welcome back {$result['user_name']}", 1, true, 'User-Menu' );
        } else {
            throw new Exception( 'Wrong password. Try again. ' );
        } 
      } else {
        throw new Exception( 'Sorry there was a problem.' );
      }
    } catch ( Exception $e ) {
      return Tools::returnMsg( $e->getMessage(), 0 );
    }
  } // end function dologinWithPostData()

  /**
   * perform the logout
   */
  public function doLogout()
  {
    // delete the session of the user
    $_SESSION = array();
    session_destroy();
    // return a little feeedback message
   return Tools::returnMsg( 'You have been logged out', 1, true, 'User-Menu' );
  }

  /**
   * simply return the current state of the user's login
   * @return boolean user's login status
   */
  public function isLoggedIn()
  {
    if ( isset( $_SESSION['user_login_status'] ) AND isset( $_SESSION['user_id'] ) AND $_SESSION['user_login_status'] == 1 )
      return true;
    // default return
    return false;
  }

  public function getUserInfo()
  {
    global $db;

    if( $this->isLoggedIn() )
    {                
      if( $result = $db->select( 'users', $this->aWhereFromSession(), '`user_name`, `user_email`' ) )
        return $result;
      else
        return false;
    }
    return false;
  }

  public function updateUserInfo()
  {
    global $db;
    try
    {
      // Initialize the array
      $aSet = array();

      // if the new email is not empty and its not the same as the curent one update
      if( !empty( $_POST['newEmail'] ) && $_POST['newEmail'] !== $_SESSION['user_email'] )
      {
        if ( strlen( $_POST['newEmail'] ) > 64 )
          throw new Exception( 'Email: cannot be longer than 64 characters' );
        
        if ( !filter_var( $_POST['newEmail'], FILTER_VALIDATE_EMAIL ) ) 
          throw new Exception( 'Email: not in a valid email format' );
        
        // lets check if the same email exists else where.
        if( !$db->Select( 'users', array( 'user_email' => $_POST['newEmail'] ), '`user_id`', '', 1 ) )
          throw new Exception( 'There was a problem finding the user, log out and try again.' );

        // password doesnt match the one in the database
        if ( $db->records == 1)
          throw new Exception( 'This email address is already in use' );

        // Add the email to our update array.
        $aSet['user_email'] = $_POST['newEmail'];
      }

      if( !empty( $_POST['currentPassword'] ) )
      {
        if ( empty( $_POST['newPassword'] ) )                             throw new Exception( 'New Password: cannot be empty' );
        if ( empty( $_POST['newPasswordCheck'] ) )                        throw new Exception( 'Re-entered password: cannot be empty' );
        if ( $_POST['newPassword'] !== $_POST['newPasswordCheck'] )       throw new Exception( 'Password: does not match re-entered password' );
        if ( strlen( $_POST['newPassword'] ) < 6)                         throw new Exception( 'Password: minimum length of 6 characters' );
        if ( $_POST['currentPassword'] == $_POST['newPassword'] )         throw new Exception( 'The current password is identical to the new one.' );

        // lets look for the old password.
        if( !$result = $db->Select( 'users', array( 'user_name' => $_SESSION['user_name'] ), '`user_password_hash`' ) )
          throw new Exception( 'There was a problem finding the user, log out and try again.' );

        // password doesnt match the one in the database
        if ( !password_verify( $_POST['currentPassword'], $result['user_password_hash'] ) )
          throw new Exception( 'The password you have entered does not match your current one .' );

        // password is ok so lets add it to the array (hash it first)
        $aSet['user_password_hash'] = $this->passHash( $_POST['newPassword'] );
      }

      // if our array has some values try to update.
      if( !empty( $aSet ) )
      {
        if( $db->Update( 'users', $aSet, $this->aWhereFromSession() ) )
        {
          // write user data into PHP SESSION
          if( !empty( $aSet['user_name']  ) ) $_SESSION['user_name']  = $aSet['user_name'];
          if( !empty( $aSet['user_email'] ) ) $_SESSION['user_email'] = $aSet['user_email'];
          $_SESSION['user_login_status'] = 1;
          return Tools::returnMsg( 'Your setting have been update.' );
        }
      }

    } catch ( Exception $e ) {
      return Tools::returnMsg( $e->getMessage(), 0 );
    }
    return Tools::returnMsg( 'Nothing has been updated.', 0 );
  }

  // returns a hashed password.
  // just for shorter code.
  public function passHash( $pass )
  {
    return password_hash( $pass, PASSWORD_DEFAULT );
  }

  public function aWhereFromSession()
  {
    return array( 'user_name'  => $_SESSION['user_name'], 
                  'user_email' => $_SESSION['user_email'],
                  'user_id'    => $_SESSION['user_id'] );
  }
} // end class