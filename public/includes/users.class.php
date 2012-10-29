<?php
/* ==============================================================================
 * 
 * @version $Id: access.class.php,v 0.93 2008/05/02 10:54:32 $
 * @copyright Copyright (c) 2007 Nick Papanotas (http://www.webdigity.com)
 * @author Nick Papanotas <nikolas@webdigity.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * 
 * Heavy customized by Tommy Marshall (tom@sirestudios.com) for the
 * CLIVO web application.
 * 	- Improved security, private classes and variables
 *	- Added Table Prefix capability
 *	- Removed insertUser, register, and email authentication
 *  - Removed/Clarified comments
 *  - Removed password encrpytion method, made default MD5
 *  - Fixed Typos
 *
 * @version admin.class.php, v 1.0.93 2009/7/2
 *
 * ==============================================================================
 */

class userAccess {

  /*Settings*/
  private $dbName = DB_NAME;
  private $dbTable  = 'users';
  private $sessionVariable = 'userSessionValue';
  
  /* 'fieldType' => 'fieldName' */
  private $tbFields = array(
  	'userId'=> 'ID', 
  	'login' => 'login',
  	'pass'  => 'pass',
  	'email' => 'email',
  	'fname' => 'fname',
  	'lname' => 'lname',
  	'add1' => 'addr1',
  	'addr2' => 'addr2',
  	'city' => 'city',
  	'state' => 'state',
  	'zip' => 'zip',
  	'created' => 'created',
  	'admin' => 'admin'
    );

  /* Some cookie settings */
  private $remTime = 2592000;
  private $remCookieName = 'uckSavePass';
  private $remCookieDomain = 'userCookie';
  
  /* Show errors */
  private $displayErrors = false;
  
  /* Do not edit after this line */
  private $userId;
  private $dbConn;
  private $userData=array();


  public function userAccess($dbConn = '', $settings = '')
  {
  	$pre=DB_TBL_PRE;
  	$this->dbTable=$pre.$this->dbTable;

	    if ( is_array($settings) ){
		    foreach ( $settings as $k => $v ){
				    if ( !isset( $this->{$k} ) ) die('Property '.$k.' does not exists. Check your settings.');
				    $this->{$k} = $v;
			}
	    }
	    $this->remCookieDomain = $this->remCookieDomain == '' ? $_SERVER['HTTP_HOST'] : $this->remCookieDomain;
	    $this->dbConn = ($dbConn=='')? mysql_connect($this->dbHost.':'.$this->dbPort, $this->dbUser, $this->dbPass):$dbConn;
	    if ( !$this->dbConn ) die(mysql_error($this->dbConn));
	    mysql_select_db($this->dbName, $this->dbConn)or die(mysql_error($this->dbConn));
	    if( !isset( $_SESSION ) ) session_start();
	    if ( !empty($_SESSION[$this->sessionVariable]) )
	    {
		    $this->loadUser( $_SESSION[$this->sessionVariable] );
	    }
	    //Maybe there is a cookie?
	    if ( isset($_COOKIE[$this->remCookieName]) && !$this->is_loaded()){
	      //echo 'I know you<br />';
	      $u = unserialize(base64_decode($_COOKIE[$this->remCookieName]));
	      $this->login($u['uname'], $u['password']);
	    }
  }
  
  public function login($uname, $password, $remember = false, $loadUser = true)
  {
    	$uname    = $this->escape($uname);
    	$password = $originalPassword = $this->escape($password);
	  	$password = md5($password);
		$res = $this->query("SELECT * FROM `{$this->dbTable}` 
		WHERE `{$this->tbFields['login']}` = '$uname' AND `{$this->tbFields['pass']}` = '$password' LIMIT 1",__LINE__);
		if ( @mysql_num_rows($res) == 0)
			return false;
		if ( $loadUser )
		{
			$this->userData = mysql_fetch_array($res);
			$this->userId = $this->userData[$this->tbFields['userId']];
			$_SESSION[$this->sessionVariable] = $this->userId;
			if ( $remember ){
			  $cookie = base64_encode(serialize(array('uname'=>$uname,'password'=>$originalPassword)));
			  $a = setcookie($this->remCookieName, 
			  $cookie,time()+$this->remTime, '/', $this->remCookieDomain);
			}
		}
		return true;
  }
  
  public function logout($redirectTo = '')
  {
    setcookie($this->remCookieName, '', time()-3600);
    $_SESSION[$this->sessionVariable] = '';
    $this->userData = '';
    if ( $redirectTo != '' && !headers_sent()){
	   header('Location: '.$redirectTo );
	   exit;//To ensure security
	}
  }
  
  public function is($prop){
  	return $this->get_property($prop)==1?true:false;
  }
  

  public function get_property($property)
  {
    if (empty($this->userId)) $this->error('No user is loaded', __LINE__);
    if (!isset($this->userData[$property])) $this->error('Unknown property <b>'.$property.'</b>', __LINE__);
    return $this->userData[$property];
  }

  public function is_active()
  {
    return $this->userData[$this->tbFields['active']];
  }
  
  public function is_loaded()
  {
    return empty($this->userId) ? false : true;
  }
  
  ////////////////////////////////////////////
  // PRIVATE FUNCTIONS
  ////////////////////////////////////////////
  
  private function query($sql, $line = 'Unknown')
  {
	$res = mysql_db_query($this->dbName, $sql, $this->dbConn);
	if ( !res )
		$this->error(mysql_error($this->dbConn), $line);
	return $res;
  }
  

  private function loadUser($userId)
  {
	$res = $this->query("SELECT * FROM `{$this->dbTable}` WHERE `{$this->tbFields['userId']}` = '".$this->escape($userId)."' LIMIT 1");
    if ( mysql_num_rows($res) == 0 )
    	return false;
    $this->userData = mysql_fetch_array($res);
    $this->userId = $userId;
    $_SESSION[$this->sessionVariable] = $this->userId;
    return true;
  }


  private function escape($str) {
    $str = get_magic_quotes_gpc()?stripslashes($str):$str;
    $str = mysql_real_escape_string($str, $this->dbConn);
    return $str;
  }
  

  private function error($error, $line = '', $die = false) {
    if ( $this->displayErrors )
    	echo '<strong>Error: </strong>'.$error.'<br /><strong>Line: </strong>'.($line==''?'Unknown':$line).'<br />';
    if ($die) exit;
    return false;
  }
}
?>