<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com> 
 * @brief Handles creating/removing/checking of cookies
 *
 */
class cookieMonster
{
	protected $db = null;
	protected $userID = null;
	/**
	* Constructor which grabs the client cookie and stores what it has locally
	* 
	* @param database that was created, this is used to invalidate the info from the database
	*/
	public function __construct($db)
	{
		$this->db = $db;
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
		$val = sprintf("%s%d%s", $userID, time(), V_URL);
		
		// we could always use mcrypt but for now I should just get crypt working
		$val = crypt($val);
		//echo $val;
		// we need to write the value to the DB so we can do checking later
		// i should probably create this function
		$this->db->updateCookieVal($userID, $val);
		
		// ok time to make the cookie
		setcookie(F_COOKIENAME, $val, 0);
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
		$this->db->updateCookieVal($userID);
		
		unset($_COOKIE[F_COOKIENAME]);
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
		if($this->userID == null and isset($_COOKIE[F_COOKIENAME]))
		{
			//print_r($_COOKIE);
			$this->userID = $this->db->findCookie($_COOKIE[F_COOKIENAME]);
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