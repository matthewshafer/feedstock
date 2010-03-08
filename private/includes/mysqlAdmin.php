<?php
require_once(V_DATABASE . ".php");

/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @extends database
 */
class databaseAdmin extends database
{
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $password
	 * @param mixed $serverAddress
	 * @param mixed $dbname
	 * @param mixed $tablePrefix
	 * @return void
	 */
	public function __construct($username, $password, $serverAddress, $dbname, $tablePrefix)
	{
		parent::__construct($username, $password, $serverAddress, $dbname, $tablePrefix);
	}
	
	/**
	 * addPost function.
	 * 
	 * @access public
	 * @param mixed $title
	 * @param mixed $data
	 * @param mixed $uri
	 * @param mixed $author
	 * @param mixed $date
	 * @param mixed $category
	 * @param mixed $tags
	 * @param mixed $draft
	 * @param mixed $id. (default: null)
	 * @return void 
	 */
	public function addPost($title, $data, $uri, $author, $date, $category, $tags, $draft, $id = null)
	{
		if($id == null)
		{
		
		}
		else
		{
			
		}
	}
	
	/**
	 * updateCookieVal function.
	 * 
	 * @access public
	 * @param mixed $userID
	 * @param mixed $val. (default: null)
	 * @return void
	 */
	public function updateCookieVal($userID, $val = null)
	{
		
	}
	
	/**
	 * findCookie function.
	 * 
	 * @access public
	 * @param mixed $val
	 * @return void
	 */
	public function findCookie($val)
	{
	
	}



}
?>