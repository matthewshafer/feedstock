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
			$query->bind_param('ssssisi', $title, $niceTitle, $uri, $data, $author, $date, $draft);
			$query->execute();
			
			if($query->affected_rows > 0)
			{
				$return = true;
			}
			
			$query->close();
		}
		else
		{
			$formattedQuery = sprintf("UPDATE %sposts SET Title=?, NiceTitle=?, URI=?, PostData=?, Author=?, Draft=? WHERE PrimaryKey=?", parent::$this->tablePrefix);
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$query->bind_param('ssssiii', $title, $niceTitle, $uri, $data, $author, $draft, $id);
			$query->execute();
			
			if($query->affected_rows > 0)
			{
				$return = true;
			}
			
			$query->close();
		}
		
		return $return;
	}
	
	public function deletePost($id)
	{
		$return = false;
		
		$formattedQuery = sprintf("DELETE FROM %sposts WHERE PrimaryKey=?", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('i', $id);
		$query->execute();
		
		if($query->affected_rows > 0)
		{
			$return = true;
		}
		$query->close();
		
		return $return;
	}
	
	public function addPage($title, $data, $niceTitle, $uri, $author, $date, $draft, $corral = null, $id = null)
	{
		$return = false;
		
		if($id == null)
		{
			if($corral == null)
			{
				$corral = -1;
			}
			
			$formattedQuery = sprintf("INSERT INTO %spages (Title, NiceTitle, URI, PageData, Author, Date, Draft, Corral) VALUES(?, ?, ?, ?, ?, ?, ?, ?)", parent::$this->tablePrefix);
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$qurey->bind_param('ssssisii', $title, $niceTitle, $uri, $data, $author, $date, $draft, $corral);
			$query->execute();
			
			if($query->affected_rows > 0)
			{
				$return = true;
			}
			$query->close();
		}
		else
		{
			if($corral == null)
			{
				$corral = -1;
			}
			
			$formattedQuery = sprintf("UPDATE %spages SET Title=?, NiceTitle=?, URI=?, PageData=?, Author=?, Draft=?, Corral=? WHERE PrimaryKey=?", parent::$this->tablePrefix);
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$query->bind_param('ssssiii', $title, $niceTitle, $uri, $data, $author, $draft, $corral);
			$query->execute();
			
			if($query->affected_rows > 0)
			{
				$return = true;
			}
			$query->close();
		}
		
		return $return;
	}
	
	public function removePage($id)
	{
		$return = false;
		
		$formattedQuery = sprintf("DELETE FROM %spages WHERE PrimaryKey=?", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('i', $id);
		$query->execute();
		
		if($query->affected_rows > 0)
		{
			$return = true;
		}
		$query->close();
		
		return $return;
	}
	
	public function addUser($username, $displayName, $passHash, $salt, $permissions = 99, $canAdministrateUsers = 0)
	{
		$return = false;
		
		$formattedQuery = sprintf("INSERT INTO %susers (loginName, displayName, PasswordHash, Salt, Permissions, CanAdminUsers) VALUES(?, ?, ?, ?, ?, ?)", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('ssssii', $username, $displayName, $passHash, $salt, $permissions, $canAdministrateUsers);
		$query->execute();
		
		if($query->affected_rows > 0)
		{
			$return = true;
		}
		$query->close();
		
		return $return;
	}
	
	// going to rewrite how users work within the next few days
	public function removeUser($userRemoveID, $currUserID)
	{
		//$formattedQuery = sprintf("",);
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
		$return = array();
		$query = sprintf("SELECT * FROM %sposts WHERE PrimaryKey='%s'", parent::$this->tablePrefix, parent::$this->dbConn->real_escape_string($id));
		
		if($result = parent::$this->dbConn->query($query))
		{
			$return = $result->fetch_assoc();
			$result->close();
		}
		
		return $return;
	}
	
	public function getPageDataByID($id)
	{
		$return = array();
		
		$query = sprintf("SELECT * FROM %spages WHERE PrimaryKey='%s'", parent::$this->tablePrefix, parent::$this->dbConn->real_escape_string($id));
		
		if($result = parent::$this->dbConn->query($query))
		{
			$return = $result->fetch_assoc();
			$result->close();
		}
		
		return $return;
	}
	
	public function getPostList($limit, $offset)
	{
	
	}
	
	public function getPageList($limit, $offset)
	{
	
	}
	
	public function addCategory($name, $niceTitle)
	{
		$return = false;
		
		$formattedQuery = sprintf("SELECT PrimaryKey FROM %scatstags WHERE URIName=? AND Type='0' LIMIT 1", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('s', $niceTitle);
		$query->execute();
		$query->store_result();
		
		$rows = $query->num_rows;
		$query->close();
		
		if($rows == 0)
		{
				$formattedQuery = sprintf("INSERT INTO %scatstags (Name, URIName, Type) VALUES(?, ?, ?)", parent::$this->tablePrefix);
				$query = parent::$this->dbConn->prepare($formattedQuery);
				// if i were to replace type in the bind_param with 0 we get a fatal error for passing something by reference.
				$type = 0;
				$query->bind_param('ssi', $name, $niceTitle, $type);
				$query->execute();
				
				if($query->affected_rows > 0)
				{
					$return = true;
				}
				$query->close();
		}
		
		return $return;
	}
	
	public function getSinglePostCategories($id)
	{
		$return = array();
		$query = sprintf("SELECT * FROM %sposts_tax WHERE PostID='%s'", parent::$this->tablePrefix, parent::$this->dbConn->real_escape_string($id));
		
		if($result = parent::$this->dbConn->query($query))
		{
			while($row = $result->fetch_assoc())
			{
				array_push($return, $row);
			}
			
			$result->close();
		}
		
		return $return;
	}
	
	public function getSinglePostTags($id)
	{
		$return = array();
		
		$formattedQuery = sprintf("SELECT CatTagID FROM %sposts_tax WHERE PostID=?", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('i', $id);
		$query->execute();
		$query->bind_result($catTagID);
		
		while($query->fetch())
		{
			array_push($return, $catTagID);
		}
		$query->close();
		
		$queryStr = implode(", ", $return);
		$return = array();
		
		$query = sprintf("SELECT Name FROM %scatstags WHERE Type='1' AND PrimaryKey IN (%s)", parent::$this->tablePrefix, $queryStr);
		
		if($result = parent::$this->dbConn->query($query))
		{
			while($row = $result->fetch_assoc())
			{
				array_push($return, $row["Name"]);
			}
			$result->close();
		}
		
		return $return;
	}
	
	public function processPostCategories($id, $catArr)
	{
		$return = false;
		if($catArr != null or !empty($catArr))
		{
			$formattedQuery = sprintf("INSERT INTO %sposts_tax (PostID, CatTagID) VALUES(?, ?)", parent::$this->tablePrefix);
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$query->bind_param('ii', $id, $key);
			
			foreach($catArr as $key)
			{
				$query->execute();
			}
			
			if($query->affected_rows > 0)
			{
				$return = true;
			}
			$query->close();
		}
		
		return $return;
	}
	
	public function unlinkPostCatsAndTags($id)
	{
		$return = false;
		$type = 1;
		
		$formattedQuery = sprintf("DELETE FROM %sposts_tax WHERE PostID=?", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('i', $id);
		$query->execute();
		
		if($query->affected_rows > 0)
		{
			$return = true;
		}
		
		$query->close();
		
		return $return;
	}
	
	public function processTags($id, $tagArray)
	{
		$return = false;
		$type = 1;
		$title = null;
		$niceTitle = null;
		$pkArray = array();
		
		if($tagArray != null or !empty($tagArray))
		{
			$formattedQuery1 = sprintf("SELECT PrimaryKey FROM %scatstags WHERE URIName=? AND Type='1' LIMIT 1", parent::$this->tablePrefix);
			$query1 = parent::$this->dbConn->prepare($formattedQuery1);
			$query1->bind_param('s', $niceTitle);
			
			
			$formattedQuery2 = sprintf("INSERT INTO %scatstags (Name, URIName, Type) VALUES(?, ?, ?)", parent::$this->tablePrefix);
			$query2 = parent::$this->dbConn->prepare($formattedQuery2);
			$query2->bind_param('ssi', $title, $niceTitle, $type);
			
			$formattedQuery3 = sprintf("INSERT INTO %sposts_tax (PostID, CatTagID) VALUES(?, ?)", parent::$this->tablePrefix);
			$query3 = parent::$this->dbConn->prepare($formattedQuery3);
			$query3->bind_param('ii', $id, $catID);
			
			foreach($tagArray as $key)
			{
				$title = $key["Title"];
				$niceTitle = $key["NiceTitle"];
				$query1->execute();
				$query1->store_result();
				$query1->bind_result($primaryKey);
				$query1->fetch();
				$rows = $query1->num_rows;
				$query1->free_result();
				
				
				if($rows == 0)
				{
					$query2->execute();
					//echo $query2->error;
					if($query2->affected_rows > 0)
					{
						$query1->execute();
						$query1->bind_result($primaryKey);
						$query1->fetch();
						array_push($pkArray, $primaryKey);
					}
				}
				else
				{
					array_push($pkArray, $primaryKey);
				}
			}
			$query1->close();
			$query2->close();
			
			$tmpCt = count($pkArray);
			print_r($pkArray);
			for($i = 0; $i < $tmpCt; $i++)
			{
				$catID = $pkArray[$i];
				$query3->execute();
			}
			
			if($query3->affected_rows > 0)
			{
				$return = true;
			}
			$query3->close();
		}
		else
		{
			$return = true;
		}
		
		return $return;
	}
}
?>