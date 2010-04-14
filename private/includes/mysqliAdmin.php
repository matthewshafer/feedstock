<?php
require_once(V_DATABASE . ".php");

/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief the database class for the admin section.  It houses a bunch of functions which is why we keep it seperate from the frontend database.
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
	public function addPost($title, $data, $niceTitle, $uri, $author, $date, $draft, $id = null)
	{
		$return = false;
		
		if($id == null)
		{
			$formattedQuery = sprintf("INSERT INTO %sposts (Title, NiceTitle, URI, PostData, Author, Date, Draft) VALUES(?, ?, ?, ?, ?, ?, ?)", parent::$this->tablePrefix);
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$query->bind_param('ssssiss', $title, $niceTitle, $uri, $data, $author, $date, $draft);
			$query->execute();
			
			if($query->affected_rows > -1)
			{
				$return = true;
			}
			
			$query->close();
		}
		else
		{
			$formattedQuery = sprintf("UPDATE %sposts SET Title=?, NiceTitle=?, URI=?, PostData=?, Author=?, Draft=? WHERE PrimaryKey=?", parent::$this->tablePrefix);
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$query->bind_param('ssssisi', $title, $niceTitle, $uri, $data, $author, $draft, $id);
			$query->execute();
			
			if($query->affected_rows > -1)
			{
				$return = true;
			}
			
			$query->close();
		}
		
		return $return;
	}
	
	public function deletePost($id)
	{
		
	}
	
	public function addPage($title, $data, $niceTitle, $uri, $author, $date, $draft, $corral = null, $id = null)
	{
		
	}
	
	public function removePage($id)
	{
	
	}
	
	public function addUser($username, $displayName, $passHash, $salt, $permissions = 99, $canAdministrateUsers = 0)
	{
	
	}
	
	public function removeUser($userRemoveID, $currUserID)
	{
	
	}
	
	public function getUserByUserName($username)
	{
		$return = null;
		
		$query = sprintf("SELECT * FROM %susers WHERE loginName='%s' LIMIT 1", parent::$this->tablePrefix, parent::$this->dbConn->real_escape_string($username));
		
		if($result = parent::$this->dbConn->query($query))
		{
			$return = $result->fetch_assoc();
			$result->close();
		}
		
		return $return;
	}
	
	public function getPostIDNiceCheckedTitle($nice)
	{
		$return = null;
		
		$formattedQuery = sprintf("SELECT PrimaryKey FROM %sposts WHERE NiceTitle=? LIMIT 1", parent::$this->tablePrefix);
		
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('s', $nice);
		$query->execute();
		$query->bind_result($result);
		
		if($query->fetch())
		{
			$return = $result;
		}
		$query->close();
		
		return $return;
	}
	
	public function checkDuplicateURI($type, $uri, $id = null)
	{
		$formattedQuery = null;
		$return = false;
		
		if($type == "post")
		{
			$formattedQuery = sprintf("SELECT PrimaryKey FROM %sposts WHERE URI=? LIMIT 1", parent::$this->tablePrefix);
		}
		else if($type == "page")
		{
			$formattedQuery = sprintf("SELECT PrimaryKey FROM %spages WHERE URI=? LIMIT 1", parent::$this->tablePrefix);
		}
		
		if($formattedQuery != null)
		{
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$query->bind_param('s', $uri);
			$query->execute();
			$query->bind_result($result);
			
			if($query->fetch())
			{
				if($id != null && $result == $id)
				{
					$return = true;
				}
			}
			else
			{
				$return = true;
			}
			
			$query->close();
		}
		
		return $return;
	}
	
	public function checkDuplicateTitle($type, $niceTitle, $id = null)
	{
		$formattedQuery = null;
		$return = false;
		
		if($type == "post")
		{
			$formattedQuery = sprintf("SELECT PrimaryKey FROM %sposts WHERE NiceTitle=? LIMIT 1", parent::$this->tablePrefix);
		}
		else if($type == "page")
		{
			$formattedQuery = sprintf("SELECT PrimaryKey FROM %spages WHERE NiceTitle=? LIMIT 1", parent::$this->tablePrefix);
		}
		
		if($formattedQuery != null)
		{
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$query->bind_param('s', $niceTitle);
			$query->execute();
			$query->bind_result($result);
			
			if($query->fetch())
			{
				if($id != null && $result == $id)
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
	
	public function updateCookieVal($userID, $val = "")
	{
		$formattedQuery = sprintf("UPDATE %susers SET CookieVal=? WHERE id=?", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('si', $val, $userID);
		$query->execute();
		$query->close();
	}
	
	public function findCookie($val)
	{
		$return = null;
		
		$formattedQuery = sprintf("SELECT id FROM %susers WHERE CookieVal=?", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('s', $val);
		$query->execute();
		$query->bind_result($result);
		
		if($query->fetch())
		{
			$return = $result;
		}
		
		$query->close();
		
		return $return;
	}
	
	public function getPostDataByID($id)
	{
	
	}
	
	public function getPageDataByID($id)
	{
	
	}
	
	public function getPostList($limit, $offset)
	{
	
	}
	
	public function getPageList($limit, $offset)
	{
	
	}
	
	public function addCategory($name, $niceTitle)
	{
	
	}
	
	public function getSinglePostCategories($id)
	{
	
	}
	
	public function getSinglePostTags($id)
	{
	
	}
	
	public function processPostCategories($id, $catArr)
	{
	
	}
	
	public function unlinkPostCatsAndTags($id)
	{
	
	}
	
	public function processTags($id, $tagArray)
	{
	
	}
	
	
}
?>