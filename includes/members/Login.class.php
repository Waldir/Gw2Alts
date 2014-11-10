<?php

/**
 * Class login
 * handles the user's login and logout process
 */
/*
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}
*/
class Login
{
  /**
  * the function "__construct()" automatically starts whenever an object of this class is created,
  * you know, when you do "$login = new Login();"
  */
  public function __construct()
  {
    // create/read session, absolutely necessary
    session_start();

    // check the possible login actions:
    // if user tried to log out (happen when user clicks logout button)
    if ( isset( $_POST["logout"] ) )
      return $this->doLogout();

    // login via post data (if user just submitted a login form)
    if ( isset( $_POST["login"] ) )
      return $this->dologinWithPostData();
  }

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
        // escape the POST stuff
        $userName = $db->SecureData( $_POST['userName'] );

        // database query, getting all the info of the selected user (allows login via email address in the
        // username field)

        // What to check for in the db
        $aWhere = array( 'user_name'  => $userName, 
                         'user_email' => $userName );

        $db->Select( 'users', $aWhere, '`user_name`, `user_email`, `user_password_hash`', '', '', false, ' OR' );

        // if this user exists
        if ( $db->iRecords < 1 )
          throw new Exception( 'This user does not exist. ' );

        // get result row (as an object)
        $result_row = $db->aArrayedResult;
        

        // using PHP 5.5's password_verify() function to check if the provided password fits
        // the hash of that user's password
        if ( password_verify( $_POST['userPassword'], $result_row['user_password_hash'] ) ) 
        {
          // write user data into PHP SESSION ( a file on your server )
          $_SESSION['userName']   = $result_row['user_name'];
          $_SESSION['user_email'] = $result_row['user_email'];
          $_SESSION['user_login_status'] = 1;
          return Tools::returnMsg( "Welcome back {$result_row['user_name']}", 1, true, 'User-Menu' );
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
  public function isUserLoggedIn()
  {
    if (isset( $_SESSION['user_login_status'] ) AND $_SESSION['user_login_status'] == 1 )
      return true;
    // default return
    return false;
  }

}
