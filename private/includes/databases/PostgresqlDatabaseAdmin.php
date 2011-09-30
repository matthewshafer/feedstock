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
	
	public function isTransactional()
	{
		return true;
	}
	
	public function startTransaction()
	{
		return pg_query(parent::$this->databaseConnection, "BEGIN WORK");
	}
	
	public function commitTransaction()
	{
		pg_query(parent::$this->databaseConnection, "COMMIT");
	}
	
	public function rollbackTransaction()
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
		// if the postId is null then we have a new post to be posted, else we have a post we are looking to update
		if($postId === null)
		{
			$formattedQuery = sprintf('INSERT INTO %sposts ("Title", "NiceTitle", "URI", "PostData", "Author", "Date", "Draft") VALUES($1, $2, $3, $4, $5, $6, $7)', parent::$this->tablePrefix);
			
			$this->runQuery($formattedQuery, array($title, $niceTitle, $uri, $data, $author, $date, $draft));	
		}
		else
		{
			if($date !== null)
			{
				$formattedQuery = sprintf('UPDATE %sposts SET "Title"=$1, "NiceTitle"=$2, "URI"=$3, "PostData"=$4, "Author"=$5, "Date"=$6, "Draft"=$7 WHERE "PrimaryKey"=$8', parent::$this->tablePrefix);
				
				$this->runQuery($formattedQuery, array($title, $niceTitle, $uri, $data, $author, $date, $draft, $postId));
			}
			else
			{
				$formattedQuery = sprintf('UPDATE %sposts SET "Title"=$1, "NiceTitle"=$2, "URI"=$3, "PostData"=$4, "Author"=$5, "Draft"=$6 WHERE "PrimaryKey"=$7', parent::$this->tablePrefix);
				
				$this->runQuery($formattedQuery, array($title, $niceTitle, $uri, $data, $author, $draft, $postId));
			}
		}
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
		$return = false;
		$formattedQuery = null;
		
		if($type == "post")
		{
			$formattedQuery = sprintf('SELECT "PrimaryKey" FROM %sposts WHERE "NiceTitle"=$1 LIMIT 1', parent::$this->tablePrefix);
		}
		else if($type == "page")
		{
			$formattedQuery = sprintf('SELECT "PrimaryKey" FROM %spages WHERE "NiceTitle"=$1 LIMIT 1', parent::$this->tablePrefix);
		}
		else if($type == "snippet")
		{
			$formattedQuery = sprintf('SELECT "PrimaryKey" FROM %ssnippet WHERE "Name"=$1 LIMIT 1', parent::$this->tablePrefix);
			//echo $formattedQuery;
		}
		
		if($formattedQuery !== false)
		{
			$result = $this->runQuery($formattedQuery, array($niceTitle));
			
			$id = pg_fetch_result($result, 0, "PrimaryKey");
			
			if($id !== false)
			{
				if($postPageSnippetId !== null && $id == $postPageSnippetId)
				{
					$return = true;
				}
			}
			else
			{
				$return = true;
			}
		}
		
		return $return;
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
		$formattedQuery = sprintf('INSERT INTO %sposts_tax ("PostID", "CatTagID") VALUES($1, $2)', parent::$this->tablePrefix);
		
		// running through all the categories selected and adding them to posts_tax
		foreach($categoryArray as $key)
		{
			$this->runQuery($formattedQuery, array($postId, $key));
		}
	}
	
	public function unlinkPostCategoriessAndTags($postId)
	{
		$formattedQuery = sprintf('DELETE FROM %sposts_tax WHERE "PostID"=?', parent::$this->tablePrefix);
		
		$this->runQuery($formattedQuery, array($postID));
	}
	
	/** not done yet **/
	public function processTags($postId, $tagArray)
	{	
		if($tagArray !== null && !empty($tagArray))
		{
			$formattedQuery1 = sprintf('SELECT "PrimaryKey" FROM %scatstags WHERE "URIName"=$1 AND "Type"=1 LIMIT 1', parent::$this->tablePrefix);
			$formattedQuery2 = sprintf('INSERT INTO %scatstags ("Name", "URIName", "Type") VALUES($1, $2, 1)', parent::$this->tablePrefix);
			$formattedQuery3 = sprintf('INSERT INTO %sposts_tax ("PostID", "CatTagID") VALUES($1, $2)', parent::$this->tablePrefix);
			
			// loops through the tagArray and does al the work
			foreach($tagArray as $key)
			{
				$query1Result = $this->runQuery($formattedQuery1, array($key['NiceTitle']));
				
				// need to add the tag to the database
				if(pg_affected_rows($query1Result === 0))
				{
					$this->runQuery($formattedQuery2, array($key['Title'], $key['NiceTitle']));
				}
			}
		}
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