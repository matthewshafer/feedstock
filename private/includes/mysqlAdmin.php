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
	
	public function addPost($title, $data, $uri, $author, $date, $category, $tags, $draft, $id = null)
	{
		if($id == null)
		{
		
		}
		else
		{
			
		}
	}



}
?>