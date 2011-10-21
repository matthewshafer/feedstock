<?php

class PostgresqlDatabase implements GenericDatabase
{

	private $username = null;
	private $password = null;
	private $serverAddress = null;
	private $serverPort = 0;
	private $databaseName = null;
	protected $databaseConnection = false;
	protected $tablePrefix = null;
	protected $connectionError = null;
	protected $checkedCategoryOrTag = null;
	protected $haveNextPage = false;
	protected $cacher = null;
	protected $haveCacher = false;
	public $debugQueries = array();
	public $queries = 0;
	protected $debugSwitch = false;

	public function __construct($username, $password, $serverAddress, $serverPort, $databaseName, $tablePrefix, $cacher = null, $lazy = true)
	{
		$this->username = $username;
		$this->password = $password;
		$this->serverAddress = $serverAddress;
		$this->serverPort = $serverPort;
		$this->databaseName = $databaseName;
		$this->tablePrefix = $tablePrefix;
		
		
		if(!$lazy === true)
		{
			$this->lazyConnect();
		}
		
		if($cacher !== null)
		{
			$this->cacher = $cacher;
			$this->haveCacher = true;
		}
	}
	
	private function lazyConnect()
	{
		$return = true;
		
		if($this->databaseConnection === false)
		{
			$connectionString = sprintf("host=%s port=%s dbname=%s user=%s password=%s", $this->serverAddress, $this->serverPort, $this->databaseName, $this->username, $this->password);
			$this->databaseConnection = pg_connect($connectionString);
			if($this->databaseConnection === false)
			{
				throw new Exception("Cannot connect to postgresql server");
				$return = false;
			}
		}
		
		return $return;
	}
	
	public function closeConnection()
	{
		pg_close($this->databaseConnection);
	}
	
	private function runQuery($query, $params)
	{
		$result = pg_query_params($this->databaseConnection, $query, $params);
		
		if(!$result)
		{
			throw new exception("query failed");
		}
		
		return $result;
	}
	
	public function getPosts($limit, $offset, $draft = false)
	{
	
	}
	
	public function getPage($uri, $draft = false)
	{
	
	}
	
	public function getSinglePost($uri, $draft = false)
	{
	
	}
	
	public function getPostCategoryOrTag($idArray, $type)
	{
	
	}
	
	public function getPostsInCategoryOrTag($type, $limit, $offset, $draft = false)
	{
	
	}
	
	//public function getCategoryOrTag($ID, $type);
	

	public function checkCategoryOrTagName($name, $type)
	{
	
	}
	
	public function listCategoriesOrTags($type)
	{
		$return = array();
		$formattedQuery = sprintf('SELECT * FROM %scatstags WHERE "Type"=$1', $this->tablePrefix);
		
		// need to see if this works when having a cacher
		// we just want to add $type to the end of $formattedQuery
		if($this->haveCacher && $this->cacher->checkExists($formattedQuery . $type))
		{
			$return = $this->cacher->getCachedData();
		}
		else if($this->lazyConnect())
		{
			// need to finish this out as we have decisions to make as to how exceptions are handled for the main database class
			$result = $this->runQuery($formattedQuery, array($type));
			
			$assocArray = pg_fetch_all($result);
			
			// debug stuff
			var_dump($assocArray);
			
			// just checking to make sure we got something back from pg_fetch_all
			if($assocArray !== false)
			{
				$return = $assocArray;
			}
		}
		
		return $return;
	}

	public function haveNextPage()
	{
		return $this->haveNextPage;
	}
	
	public function getCorralByName($name)
	{
	
	}
	
	public function getSnippetByName($name)
	{
	
	}
	
	public function enableDebug()
	{
		$this->debugSwitch = true;
	}
}

?>