<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com> 
 * @brief Handles creating/removing/checking of cookies
 *
 */
class CookieMonster
{
	protected $database = null;
	protected $userID = null;
	protected $cookieName;
	protected $siteUrl;
	
	/**
	* Constructor which grabs the client cookie and stores what it has locally
	* 
	* @param database that was created, this is used to invalidate the info from the database
	*/
	public function __construct($database, $cookieName, $siteUrl)
	{
		$this->database = $database;
		$this->cookieName = $cookieName;
		$this->siteUrl = $siteUrl;
	}
	
	/**
	 * createCookie function.
	 * 
	 * @access public
	 * @param mixed $userID
	 * @return void
	 */
	public function createCookie($userID)
	{
		$val = null;
		
		// this is only for login, since we can't set a cookie and then read it right away
		$this->userID = $userID;
		//echo $userID;
		// need to generate the value for the cookie
		$val = sprintf("%s%d%s", $userID, $_SERVER['REQUEST_TIME'], $this->siteUrl);
		
		// we could always use mcrypt but for now I should just get crypt working
		$val = crypt($val);
		//echo $val;
		// we need to write the value to the DB so we can do checking later
		// i should probably create this function
		$this->database->updateCookieVal($userID, $val);
		
		// ok time to make the cookie
		setcookie($this->cookieName, $val, 0);
	}
	
	/**
	 * removeCookie function.
	 * 
	 * @access public
	 * @param mixed $userID
	 * @return void
	 */
	public function removeCookie($userID)
	{
		// remove the cookie from the db
		$this->database->updateCookieVal($userID);
		
		unset($_COOKIE[$this->cookieName]);
	}
	
	/**
	 * checkCookie function.
	 * 
	 * @access public
	 * @return Boolean
	 */
	public function checkCookie()
	{
		$return = false;
		// if the db is null the cookie doesn't exist
		if($this->userID == null and isset($_COOKIE[$this->cookieName]))
		{
			//print_r($_COOKIE);
			$this->userID = $this->database->findCookie($_COOKIE[$this->cookieName]);
		}
		
		if($this->userID != null)
		{
			//print_r($_COOKIE);
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * getUserID function.
	 * 
	 * @access public
	 * @return Integer
	 */
	public function getUserID()
	{
		return $this->userID;
	}
}
?>