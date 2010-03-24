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
	public function addPost($title, $data, $niceTitle, $uri, $author, $date, $category, $tags, $draft, $id = null)
	{
		$query = null;
		
		// need to do something with category and tags
		// for real
		
		if($id == null)
		{
			// add the new post
			$query = sprintf(
			"INSERT INTO %sposts (Title, NiceTitle, URI, PostData, Author, Date, Draft) VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
			parent::$this->tablePrefix, 
			mysql_real_escape_string($title, parent::$this->dbConn), 
			mysql_real_escape_string($niceTitle, parent::$this->dbConn), 
			mysql_real_escape_string($uri, parent::$this->dbConn), 
			mysql_real_escape_string($data, parent::$this->dbConn), 
			mysql_real_escape_string($author, parent::$this->dbConn), 
			mysql_real_escape_string($date, parent::$this->dbConn), 
			mysql_real_escape_string($draft, parent::$this->dbConn)
			);
		}
		// need to make sure ID is an int somewhere
		else
		{
			// update the post with this ID
			$query = sprintf(
			"UPDATE %sposts SET Title='%s', URI='%s', PostData='%s', Author='%s', Date='%s', Draft='%s' WHERE id='%s'", 
			parent::$this->tablePrefix,
			mysql_real_escape_string($title, parent::$this->dbConn), 
			mysql_real_escape_string($uri, parent::$this->dbConn), 
			mysql_real_escape_string($data, parent::$this->dbConn), 
			mysql_real_escape_string($author, parent::$this->dbConn), 
			mysql_real_escape_string($date, parent::$this->dbConn), 
			mysql_real_escape_string($draft, parent::$this->dbConn), 
			mysql_real_escape_string($id, parent::$this->dbConn)
			);
		}
		
		$result = false;
		
		if($query != null)
		{
			$result = mysql_query($query, parent::$this->dbConn);
		}
		
		return $result;
	}
	
	public function deletePost($id)
	{
		
	}
	
	public function checkDuplicateURI($type, $uri)
	{
		$query = null;
		$return = false;
		
		if($type == "post")
		{
			$query = sprintf("SELECT * FROM %sposts WHERE URI='%s'", parent::$this->tablePrefix, mysql_real_escape_string($uri, parent::$this->dbConn));
		}
		else if($type == "page")
		{
			$query = sprintf("SELECT * FROM %spages WHERE URI='%s'", parent::$this->tablePrefix, mysql_real_escape_string($uri, parent::$this->dbConn));
		}
		
		if($query != null)
		{
			$result = mysql_query($query, parent::$this->dbConn);
			
			if($result != null)
			{
				$return = true;
			}
		}
		
		return $return;
	}
	
	/**
	 * updateCookieVal function.
	 * 
	 * @access public
	 * @param mixed $userID
	 * @param mixed $val. (default: null)
	 * @return void
	 */
	public function updateCookieVal($userID, $val = "")
	{
		$query = sprintf(
		"UPDATE %susers SET CookieVal='%s' WHERE id='%s'", 
		parent::$this->tablePrefix, 
		mysql_real_escape_string($val, parent::$this->dbConn), 
		mysql_real_escape_string($userID, parent::$this->dbConn)
		);
		
		$result = mysql_query($query, parent::$this->dbConn);
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
		$query = sprintf("SELECT id FROM %susers WHERE CookieVal='%s'", parent::$this->tablePrefix, mysql_real_escape_string($val, parent::$this->dbConn));
		
		$result = mysql_query($query, parent::$this->dbConn);
		
		$array = mysql_fetch_assoc($result);
		
		//array_push($array, mysql_fetch_assoc($result));
		$return = null;
		if(isset($array["id"]))
		{
			$return = $array["id"];
		}
		
		return $return;
	}
	
	public function getUserByUserName($username)
	{
		$query = sprintf("SELECT * FROM %susers WHERE loginName='%s' LIMIT 1", parent::$this->tablePrefix, mysql_real_escape_string($username, parent::$this->dbConn));
		
		$result = mysql_query($query, parent::$this->dbConn);
		
		$return = array();
		
		//while($temp = mysql_fetch_assoc($result))
		//{
			//array_push($return, $temp);
		//}
		
		return mysql_fetch_assoc($result);
	}
	
	public function addUser($username, $displayName, $passHash, $salt, $permissions = 99, $canAdministrateUsers = 0)
	{
		$query = sprintf(
		"INSERT INTO %susers (loginName, displayName, PasswordHash, Salt, Permissions, CanAdminUsers) VALUES('%s', '%s', '%s', '%s', '%s', '%s')", 
		parent::$this->tablePrefix, 
		mysql_real_escape_string($username, parent::$this->dbConn), 
		mysql_real_escape_string($displayName, parent::$this->dbConn), 
		mysql_real_escape_string($passHash, parent::$this->dbConn), 
		mysql_real_escape_string($salt, parent::$this->dbConn), 
		mysql_real_escape_string($permissions, parent::$this->dbConn), 
		mysql_real_escape_string($canAdministrateUsers, parent::$this->dbConn)
		);
		
		return mysql_query($query, parent::$this->dbConn);
	}
	
	
	public function removeUser($userRemoveID, $currUserID)
	{
		// need to make sure the user being removed allowed to do this
		// so pretty much make sure we are not removing ourselves, see if we can administrate users, and lastly make sure the user has a lower rank than us
		$query = sprintf("SELECT * FROM %susers WHERE id='%s'", parent::$this->tablePrefix, mysql_real_escape_string($currUserID, parent::$this->dbConn));
		
		$result = mysql_query($query, parent::$this->dbConn);
		
		$temp = mysql_fetch_assoc($result);
		
		$query2 = sprintf("SELECT * FROM %susers WHERE id='%s'", parent::$this->tablePrefix, mysql_real_escape_string($userRemoveID, parent::$this->dbConn));
		
		$result2 = mysql_query($query2, parent::$this->dbConn);
		
		$temp2 = mysql_fetch_assoc($result2);
		
		$return = false;
		
		if(isset($temp["Permissions"], $temp["CanAdminUsers"], $temp2["Permissions"]))
		{
			if($temp["Permissions"] > $temp2["Permissions"] and $temp["CanAdminUsers"])
			{
				// I could probably grab the data from $temp2 as to the user being removed and use that over $userRemoveID
				$query3 = sprintf("DELETE FROM %susers WHERE id='%s'", parent::$this->tablePrefix, mysql_real_escape_string($userRemoveID, parent::$this->dbConn));
				
				$result3 = mysql_query($query3, parent::$this->dbConn);
				
				$return = true;
			}
		}
		
		return $return;
	}
	
	public function checkNiceTitleExists($title, $id = null)
	{
		$query = sprintf("SELECT * from %sposts WHERE NiceTitle='%s' LIMIT 1", parent::$this->tablePrefix, mysql_real_escape_string($title, parent::$this->dbConn));
		
		$result = mysql_query($query, parent::$this->dbConn);
		
		$temp = mysql_fetch_assoc($result);
		
		if(is_null($temp["NiceTitle"]))
		{
			$return = false;
		}
		else
		{
			if($id != null and $temp["PrimaryKey"] == $id)
			{
				$return = false;
			}
			else
			{
				$return = true;
			}
		}
		
		return $return;
	}
	
	// all these next guys need to get the comment/tag/corral data also and that needs to be pushed into the assoc array.
	// I'll get this done over the weekend since its almost 2am and im pretty sleepy.
	
	/**
	 * getPostDataByID function.
	 * 
	 * @access public
	 * @param mixed $id
	 * @return Array of Post info
	 */
	public function getPostDataByID($id)
	{
		
	}
	
	/**
	 * getPageDataByID function.
	 * 
	 * @access public
	 * @param mixed $id
	 * @return Array of Page info
	 */
	public function getPageDataByID($id)
	{
		
	}
	
	/**
	 * getPostList function.
	 * 
	 * @access public
	 * @param mixed $offset
	 * @return Array of Posts
	 */
	public function getPostList($offset)
	{
		
	}
	
	/**
	 * getPageList function.
	 * 
	 * @access public
	 * @param mixed $offset
	 * @return Array of Pages
	 */
	public function getPageList($offset)
	{
		
	}


}
?>