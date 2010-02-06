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
	private $router = null;
	private $db = null;

	public function __construct()
	{
		require_once("../config.php");
		$this->address = $address;
		$this->password = $password;
		$this->username = $username;
		$this->database = $database;
		$this->tableprefix = $tableprefix;
	}




}
?>