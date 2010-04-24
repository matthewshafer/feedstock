<?php
require_once(V_DATABASE . ".php");

/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief the database class for the admin section.  It houses a bunch of functions which is why we keep it seperate from the frontend database.
 * @extends database
 */
class mysqliDatabaseAdmin extends mysqliDatabase
{
	/**
	 * __construct function.
	 * 
	 * @brief Creates the database connection
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
	 * @brief Add's a post to the database
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
	 * @return True if able to insert or update. False if Not able to insert or update.
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
	
	/**
	 * deletePost function.
	 * 
	 * @brief Removes a post from the database
	 * @access public
	 * @param mixed $id
	 * @return True if able to delete. False if not able to delete.
	 */
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
	
	/**
	 * addPage function.
	 * 
	 * @brief Add's a page to the database
	 * @access public
	 * @param mixed $title
	 * @param mixed $data
	 * @param mixed $niceTitle
	 * @param mixed $uri
	 * @param mixed $author
	 * @param mixed $date
	 * @param mixed $draft
	 * @param mixed $corral. (default: null)
	 * @param mixed $id. (default: null)
	 * @return True if able to Insert ot Update. False if not able ti Insert or Update
	 */
	public function addPage($title, $data, $niceTitle, $uri, $author, $date, $draft, $corral = null, $id = null)
	{
		$return = false;
		
		// this way we put null into the database so we can do WHERE Corral IS NOT NULL in the sql later
		if($corral == "")
		{
			$corral = null;
		}
		
		if($id == null)
		{
			
			$formattedQuery = sprintf("INSERT INTO %spages (Title, NiceTitle, URI, PageData, Author, Date, Draft, Corral) VALUES(?, ?, ?, ?, ?, ?, ?, ?)", parent::$this->tablePrefix);
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$query->bind_param('ssssisis', $title, $niceTitle, $uri, $data, $author, $date, $draft, $corral);
			$query->execute();
			
			if($query->affected_rows > 0)
			{
				$return = true;
			}
			$query->close();
		}
		else
		{	
			$formattedQuery = sprintf("UPDATE %spages SET Title=?, NiceTitle=?, URI=?, PageData=?, Author=?, Draft=?, Corral=? WHERE PrimaryKey=?", parent::$this->tablePrefix);
			$query = parent::$this->dbConn->prepare($formattedQuery);
			$query->bind_param('ssssiisi', $title, $niceTitle, $uri, $data, $author, $draft, $corral, $id);
			$query->execute();
			
			if($query->affected_rows > 0)
			{
				$return = true;
			}
			$query->close();
		}
		
		return $return;
	}
	
	/**
	 * removePage function.
	 * 
	 * @brief Removes a page from the database
	 * @access public
	 * @param mixed $id
	 * @return True if able to delete, false if not.
	 */
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
	
	/**
	 * addUser function.
	 * 
	 * @brief Add's a user to the database
	 * @access public
	 * @param mixed $username
	 * @param mixed $displayName
	 * @param mixed $passHash
	 * @param mixed $salt
	 * @param int $permissions. (default: 99)
	 * @param int $canAdministrateUsers. (default: 0)
	 * @return True if able to insert, False if not.
	 */
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
	
	/**
	 * removeUser function.
	 * 
	 * @brief Removes a user from the database if the user deleting them has a higher user level and is allowed to administrate users.
	 * @access public
	 * @param mixed $userRemoveID
	 * @param mixed $currUserID
	 * @return Boolean, True if the delete worked, false if it failed some how.
	 */
	public function removeUser($userRemoveID, $currUserID)
	{
		$return = false;
		$formattedQuery = sprintf("SELECT Permissions, CanAdminUsers FROM %susers WHERE id=?", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('i', $id);
		$id = $currUserID;
		$query->execute();
		$query->bind_result($permissions, $canAdmin);
		$query->fetch();
		$currPerm = $permissions;
		$currAdmin = $canAdmin;
		$query->free_result();
		$id = $userRemoveID;
		$query->execute();
		// not sure if this is needed again
		$query->bind_result($permissions, $canAdmin);
		$query->fetch();
		
		if($currPerm < $permissions && $currAdmin === 1)
		{
			$formattedQuery2 = sprintf("DELETE FROM %susers WHERE id=?", parent::$this->tablePrefix);
			$query2 = parent::$this->dbConn->prepare($formattedQuery2);
			$query2->bind_param('i', $userRemoveID);
			$query->execute();
			
			if($query2->affected_rows > 0)
			{
				$return = true;
			}
			$query2->close();
		}
		$query->close();
		
		return $return;
	}
	
	/**
	 * getUserByUserName function.
	 * 
	 * @brief Gets a user's information based on their username
	 * @access public
	 * @param mixed $username
	 * @return Array of user Information.  If not found then null.
	 */
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
	
	/**
	 * getPostIDNiceCheckedTitle function.
	 * 
	 * @brief Returns the postID that the title passed in has
	 * @access public
	 * @param mixed $nice
	 * @return Post's key (integer).  If not found then null.
	 */
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
	
	/**
	 * checkDuplicateURI function.
	 * 
	 * @brief checks to see if a URI already exists in the database
	 * @access public
	 * @param mixed $type
	 * @param mixed $uri
	 * @param mixed $id. (default: null)
	 * @return True if the URI doesn't exist. False if it exists.
	 */
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
	
	/**
	 * checkDuplicateTitle function.
	 * 
	 * @brief Checks to see if a title already exists in the database
	 * @access public
	 * @param mixed $type
	 * @param mixed $niceTitle
	 * @param mixed $id. (default: null)
	 * @return True if the title doesn't exist. False if it exists.
	 */
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
	
	/**
	 * updateCookieVal function.
	 * 
	 * @brief Updates the user's cookievalue in the database
	 * @access public
	 * @param mixed $userID
	 * @param string $val. (default: "")
	 * @return void
	 */
	public function updateCookieVal($userID, $val = "")
	{
		$formattedQuery = sprintf("UPDATE %susers SET CookieVal=? WHERE id=?", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('si', $val, $userID);
		$query->execute();
		$query->close();
	}
	
	/**
	 * findCookie function.
	 * 
	 * @brief finds the userID based on the cookievalue passed in
	 * @access public
	 * @param mixed $val
	 * @return The ID (integer) of the user. Null if doesn't exist.
	 */
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
	
	/**
	 * getPostDataByID function.
	 * 
	 * @brief Returns the post data based on the ID passed in.
	 * @access public
	 * @param mixed $id
	 * @return Array with post information
	 */
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
	
	/**
	 * getPageDataByID function.
	 * 
	 * @brief Returns the page data based in the ID passed in.
	 * @access public
	 * @param mixed $id
	 * @return Array with page information
	 */
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
		$return = array();
		
		$query = sprintf("SELECT * FROM %spages ORDER BY PrimaryKey desc LIMIT %d OFFSET %d", parent::$this->tablePrefix, parent::$this->dbConn->real_escape_string($limit), parent::$this->dbConn->real_escape_string($offset));
		
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
	
	/**
	 * addCategory function.
	 * 
	 * @brief Add's a category to the database
	 * @access public
	 * @param mixed $name
	 * @param mixed $niceTitle
	 * @return True if category was added.  False if not added.
	 */
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
	
	/**
	 * getSinglePostCategories function.
	 * 
	 * @brief Returns the category ID's based on the postID passed in.
	 * @access public
	 * @param mixed $id
	 * @return Array with post categories
	 */
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
	
	/**
	 * getSinglePostTags function.
	 * 
	 * @brief Returns the post tags by the postID passed in.
	 * @access public
	 * @param mixed $id
	 * @return Array with post tags
	 */
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
	
	/**
	 * processPostCategories function.
	 * 
	 * @brief Insert the categories for the post into the database.
	 * @access public
	 * @param mixed $id
	 * @param mixed $catArr
	 * @return True if able to insert categories for post. False if not able to.
	 */
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
	
	/**
	 * unlinkPostCatsAndTags function.
	 * 
	 * @brief deletes the categories and tags for the post from the database
	 * @access public
	 * @param mixed $id
	 * @return True if we were able to Delete.  False if we were unable to Delete or there was nothing to delete.
	 */
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
	
	/**
	 * processTags function.
	 * 
	 * @brief Inserts the tags into the database if they don't exist
	 * @access public
	 * @param mixed $id
	 * @param mixed $tagArray
	 * @return True if tags were processed or if there was nothing to process.  False if there was an error.
	 */
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
	
	/**
	 * getCorralList function.
	 * 
	 * @brief Grabs a list of all of the Distinct corrals.
	 * @access public
	 * @return Array containing the names of corral's or an empty array if there are none
	 */
	public function getCorralList()
	{
		$return = array();
		$query = sprintf("SELECT DISTINCT Corral FROM %spages WHERE Corral IS NOT NULL", parent::$this->tablePrefix);
		
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
	
	/**
	 * getPagesInCorral function.
	 * 
	 * @brief Grabs a list of all of the pages inside of a corral.
	 * @access public
	 * @param mixed $name
	 * @return Array containing the names and primary key's of pages in a specific corral or an empty array if there are none
	 */
	public function getPagesInCorral($name)
	{
		$return = array();
		$formattedQuery = sprintf("SELECT PrimaryKey, Title FROM %spages WHERE Corral=?", parent::$this->tablePrefix);
		$query = parent::$this->dbConn->prepare($formattedQuery);
		$query->bind_param('s', $name);
		$query->execute();
		$query->bind_result($pk, $title);
		
		$count = 0;
		
		while($query->fetch())
		{
			$return[$count] = array();
			$return[$count]["PrimaryKey"] = $pk;
			$return[$count]["Corral"] = $title;
			$count++;
		}
		$query->close();
		
		return $return;
	}
}
?>