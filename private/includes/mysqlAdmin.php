<?php
require_once("mysql.php");

class databaseAdmin extends database
{
	public function __construct($username, $password, $serverAddress, $dbname, $tablePrefix)
	{
		parent::__construct($username, $password, $serverAddress, $dbname, $tablePrefix);
	}



}
?>