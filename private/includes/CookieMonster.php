<?php
/**
 * Handles creating/removing/checking of cookies
 *
 * @author Matthew Shafer <matt@niftystopwatch.com> 
 *
 */
class CookieMonster
{
	protected $database = null;
	protected $userID = null;
	protected $cookieName;
	protected $siteUrl;
	


	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param GenericDatabaseAdmin $database
	 * @param mixed $cookieName
	 * @param mixed $siteUrl
	 * @return void
	 */
	public function __construct(GenericDatabaseAdmin $database, $cookieName, $siteUrl)
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
		
		// hashing the cookie value with whirlpool
		$val = hash('whirlpool', $val);
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
	 * if the userID already exists, so this was previously called then no lookup in the database is done, else we do a lookup
	 * we could possibly remove the second check by rewriting some stuff, i'll have to look into it
	 * @access public
	 * @return boolean true if the cookie exists for that userid or false if it doesn't
	 */
	public function checkCookie()
	{
		$return = false;
		// if the db is null the cookie doesn't exist
		if($this->userID === null && isset($_COOKIE[$this->cookieName]))
		{
			//print_r($_COOKIE);
			$this->userID = $this->database->findCookie($_COOKIE[$this->cookieName]);
		}
		
		if($this->userID !== null)
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
	 * @return int|null returns the user id if one exists or null if it doesn't
	 */
	public function getUserID()
	{
		return $this->userID;
	}
}
?>