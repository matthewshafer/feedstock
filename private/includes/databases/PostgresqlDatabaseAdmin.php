<?php
require_once("PostgresqlDatabase.php");

class PostgresqlDatabaseAdmin extends PostgresqlDatabase implements GenericDatabaseAdmin
{

	/**
	 * __construct function.
	 * 
	 * @brief Creates the database connection
	 * @access public
	 * @param mixed $username
	 * @param mixed $password
	 * @param mixed $serverAddress
	 * @param mixed $databaseName
	 * @param mixed $tablePrefix
	 * @return void
	 */
	public function __construct($username, $password, $serverAddress, $serverPort, $databaseName, $tablePrefix, $cacher = null, $lazy = true)
	{
		parent::__construct($username, $password, $serverAddress, $serverPort, $databaseName, $tablePrefix, null, false);
	}
	
	private function startTransaction()
	{
		return pg_query(parent::$this->databaseConnection, "BEGIN WORK");
	}
	
	private function commitTransaction()
	{
		pg_query(parent::$this->databaseConnection, "COMMIT");
	}
	
	private function rollbackTransaction()
	{
		return pg_query(parent::$this->databaseConnection, "ROLLBACK");
	}
	
	private function runQuery($query, $params)
	{
		$result = pg_query_params(parent::$this->databaseConnection, $query, $params);
		
		if(!$result)
		{
			throw new exception("query failed");
		}
		
		return $result;
	}

	public function addPost($title, $data, $niceTitle, $uri, $author, $date, $draft, $postId = null)
	{
	
	}
	
	public function deletePost($postId)
	{
	
	}
	
	public function addPage($title, $data, $niceTitle, $uri, $author, $date, $draft, $corral = null, $pageId = null)
	{
	
	}
	
	public function removePage($pageId)
	{
	
	}
	
	public function addUser($username, $displayName, $passwordHash, $salt, $permissions = 99, $canAdministrateUsers = 0)
	{
		$return = false;
		
		$formattedQuery = sprintf('INSERT INTO %susers ("loginName", "displayName", "PasswordHash", "Salt", "Permissions", "CanAdminUsers") VALUES($1, $2, $3, $4, $5, $6)', parent::$this->tablePrefix);
		
		$this->startTransaction();
		
		try
		{
			$this->runQuery($formattedQuery, array($username, $displayName, $passwordHash, $salt, $permissions, $canAdministrateUsers));
			$this->commitTransaction();
			$return = true;
		}
		catch(exception $e)
		{
			$this->rollbackTransaction();
		}
		
		return $return;
	}
	
	public function removeUser($userIdToRemove, $currentUserID)
	{
	
	}
	
	public function getUserByUserName($username)
	{
		$return = null;
		
		$query = sprintf('SELECT * FROM %susers WHERE "loginName"=$1 LIMIT 1', parent::$this->tablePrefix);
		
		try
		{
			$result = $this->runQuery($query, array($username));
			$return = pg_fetch_assoc($result);
		}
		catch(exception $e)
		{
		
		}
		
		return $return;
	}
	
	public function getPostIdNiceCheckedTitle($niceTitle)
	{
	
	}
	
	public function checkDuplicateUri($type, $uri, $postOrPageId = null)
	{
	
	}
	
	public function checkDuplicateTitle($type, $niceTitle, $postPageSnippetId = null)
	{
	
	}
	
	public function updateCookieVal($userId, $cookieValue = "")
	{
		$return = false;
		$query = sprintf('UPDATE %susers SET "CookieVal"=$1 WHERE "id"=$2', parent::$this->tablePrefix);
		
		$this->startTransaction();
		
		try
		{
			$this->runQuery($query, array($cookieValue, $userId));
			$this->commitTransaction();
		}
		catch(exception $e)
		{
			$this->rollbackTransaction();
		}
		
		return $return;
	}
	
	public function findCookie($cookieValue)
	{
		$return = null;
		
		$query = sprintf('SELECT "id" FROM %susers WHERE "CookieVal"=$1', parent::$this->tablePrefix);
		
		try
		{
			$result = $this->runQuery($query, array($cookieValue));
			$return = pg_fetch_result($result, 0, "id");
		}
		catch(exception $e)
		{
		
		}
		
		return $return;
	}
	
	public function getPostDataById($postId)
	{
	
	}
	
	public function getPageDataById($pageId)
	{
	
	}
	
	//public function getPostList($limit, $offset);
	
	public function getPageList($limit, $offset)
	{
	
	}
	
	public function addCategory($name, $niceTitle)
	{
	
	}
	
	public function getSinglePostCategories($postId)
	{
	
	}
	
	public function getSinglePostTags($postId)
	{
	
	}
	
	public function processPostCategories($postId, $categoryArray)
	{
	
	}
	
	public function unlinkPostCategoriessAndTags($postId)
	{
	
	}
	
	public function processTags($postId, $tagArray)
	{
	
	}
	
	public function getCorralList()
	{
	
	}
	
	public function getPagesInCorral($name)
	{
	
	}
	
	public function addSnippet($name, $data, $snippetId = null)
	{
	
	}
	
	public function removeSnippet($snippetId)
	{
	
	}
	
	public function getSnippetList()
	{
	
	}
	
	public function getSnippetById($snippetId)
	{
	
	}
	
	public function getAllPostsSitemap()
	{
	
	}
	
	public function getAllPagesSitemap()
	{
	
	}
}

?>