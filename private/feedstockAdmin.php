<?php

class feedstockAdmin
{
	private $username = null;
	private $password = null;
	private $address = null;
	private $database = null;
	private $tableprefix = null;
	private $templateEngine = null;
	private $templateLoader = null;
	private $postManager = null;
	private $dbAdmin = null;
	private $cookieMonster = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		require_once("../config.php");
		$this->address = $address;
		$this->password = $password;
		$this->username = $username;
		$this->database = $database;
		$this->tableprefix = $tableprefix;
		
		require_once("includes/router.php");
		
		$this->router = new router(F_ADMINHTACCESS, F_ADMINBASE);
		
		require_once("includes/postManager.php");
		
		$this->postManager = new postManager();
		
		require_once("includes/" . V_DATABASE . "Admin.php");
		
		$this->dbAdmin = new databaseAdmin($this->username, $this->password, $this->address, $this->database, $this->tableprefix);
		
		require_once("includes/cookieMonster.php");
		
		$this->cookieMonster = new cookieMonster($this->dbAdmin);
		
		require_once("includes/templateEngineAdmin.php");
		
		require_once("includes/templateLoader.php");
		
		if($this->postManager->getPostType() == "LOGIN")
		{
			// check the login info and then set the cookie
			if($this->postManager->getPostByName("USERNAME") != null and $this->postManager->getPostByName("PASSWORD") != null)
			{
				$userArray = $this->dbAdmin->getUser($this->postManager->getPostByName("USERNAME"));
				
				// run some encryption on the password entered
				
				// check that with what we have in the database
				
			}
			
		}
		
		if(!$this->cookieMonster->checkCookie())
		{
			// user is logged in
		
		}
		else
		{
			// user isn't logged in
		
		}
	}
	
	private function makePasswordHash($p, $s)
	{	
		// create some var's we need for later
		$s2 = F_PSALT;
		$preSalt = null;
		$s2len = strlen($s2);
		$slen = strlen($s);
		$start = 0;
		
		// figure out which string is longer
		if($s2len < $slen)
		{
			$length = $slen;
		}
		else
		{
			$length = $s2len;
		}
		
		// mix up the two salt's into one new salt
		while($start < $length)
		{
			if($start < $slen)
			{
				$preSalt .= $s[$start];
			}
			
			if($start < $s2len)
			{
				$preSalt .= $s2[$start];
			}
			
			$start++;
		}
		
		// split up the password into two parts
		$password = str_split($p, (strlen($p)/2)+1);
		
		// same deal with the salt
		$salt = str_split($preSalt, (strlen($preSalt)/2)+1);
		
		// hash them using whirlpool with the salts added
		$hash = hash('whirlpool', $password[0].$salt[0].$password[1].$salt[1]);
		
		return $hash;
	}

}
?>