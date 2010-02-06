<?php

class database
{
	protected $dbConn = null;
	protected $dBaseValid = null;
	protected $tablePrefix = null;
	
	public function __construct($username, $password, $serverAddress, $dbname, $tablePrefix)
	{
		$this->dbConn = mysql_connect($serverAddress, $username, $password);
		
		if(!is_resource($this->dbConn))
		{
			die("Unable to connect to the Database Server");
		}
		$this->dBaseValid = mysql_select_db($dbname, $this->dbConn);
		
		if(!$this->dBaseValid)
		{
			die("Unable to connect to the database");
		}
		
		$this->tablePrefix = mysql_real_escape_string($tablePrefix, $this->dbConn);
	}
	
	public function closeConnection()
	{
		mysql_close($this->dbConn);
	}
	
	// gets the posts from the start to finish locations
	public function getPosts($offset)
	{
		//$query = "SELECT * FROM " . $this->tablePrefix . "posts order by date DESC LIMIT 10 OFFSET " . $offset;
		$query = sprintf("SELECT * FROM %sposts order by date DESC LIMIT 10 OFFSET %s", $this->tablePrefix, mysql_real_escape_string($offset, $this->dbConn));
		$result = mysql_query($query , $this->dbConn);
		
		$return = array();
		// need to check if anything is in the array
		
		while($temp = mysql_fetch_assoc($result))
		{
			array_push($return, $temp);
		}
		
		//print_r($return);
		return $return;
	}
	
	public function getPage($URI)
	{
		//$query = "SELECT * FROM " . $this->tablePrefix . "pages WHERE URI='/" . $URI . "'";
		$query = sprintf("SELECT * FROM %spages WHERE URI='/%s'", $this->tablePrefix, mysql_real_escape_string($URI, $this->dbConn));
		//echo $query;
		$result = mysql_query($query , $this->dbConn);
		
		$return = array();
		
		while($temp = mysql_fetch_assoc($result))
		{
			array_push($return, $temp);
		}

		
		return $return;
	}
	
	public function getSinglePost($URI)
	{
		//$query = "SELECT * FROM " . $this->tablePrefix . "posts WHERE URI='/" . $URI . "'";
		$query = sprintf("SELECT * FROM %sposts WHERE URI='/%s'", $this->tablePrefix, mysql_real_escape_string($URI, $this->dbConn));
		$result = mysql_query($query , $this->dbConn);
		
		$return = array();
		
		while($temp = mysql_fetch_assoc($result))
		{
			array_push($return, $temp);
		}
		
		return $return;
	}
	
	public function checkCategoryTagName($name, $type)
	{
		$return = false;
		$query = sprintf("SELECT * FROM %scatstags WHERE URIName='%s' AND Type='%s'", $this->tablePrefix, mysql_real_escape_string($name, $this->dbConn), mysql_real_escape_string($type, $this->dbConn));
		$result = mysql_query($query, $this->dbConn);
		
		if(mysql_fetch_assoc($result))
		{
			$return = true;
		}
		
		return $return;
	}
	
	public function listCategoriesOrTags($type)
	{
		//$query = "SELECT * FROM " . $this->tablePrefix . "tags LIMIT" . $number;
		$query = sprintf("SELECT * FROM %scatstags WHERE Type='%s'", $this->tablePrefix, mysql_real_escape_string($type, $this->dbConn));
		$result = mysql_query($query, $this->dbConn);
		
		$return = array();
		
		while($temp = mysql_fetch_assoc($result))
		{
			array_push($return, $temp);
		}
		
		return $return;
	}
	
	public function getPostCategoryOrTag($ID, $type)
	{
		$query = sprintf("SELECT CatTagID FROM %sposts_tax WHERE PostID='%s'", $this->tablePrefix, mysql_real_escape_string($ID, $this->dbConn));
		$result = mysql_query($query, $this->dbConn);
		
		$return = array();
		
		while($temp = mysql_fetch_assoc($result))
		{
			//print_r($temp);
			$query2 = sprintf("SELECT * FROM %scatstags WHERE TYPE='%s' AND PrimaryKey='%s'", $this->tablePrefix, mysql_real_escape_string($type, $this->dbConn), mysql_real_escape_string($temp["CatTagID"], $this->dbConn));
			$result2 = mysql_query($query2);
			
			// see if there is a better way to implement this
			// could probably do without the loop
			while($temp = mysql_fetch_assoc($result2))
			{
				if($temp != null)
				{
					array_push($return, $temp);
				}
			}
		}
		
		//print_r($return);
		return $return;
	}
	
	// still need to finish the getting the posts part.
	public function getPostsInCategoryOrTag($URIName, $type)
	{
		$query = sprintf("SELECT PrimaryKey FROM %scatstags WHERE URIName='%s' AND Type='%s'", $this->tablePrefix, mysql_real_escape_string($URIName, $this->dbConn), mysql_real_escape_string($type, $this->dbConn));
		$result = mysql_query($query, $this->dbConn);
		
		$return = array();
		
		$result2 = null;
		
		$temp = mysql_fetch_assoc($result);
		
		//print_r($temp);
		
		$query2 = sprintf("SELECT PostID FROM %sposts_tax WHERE CatTagID='%s'", $this->tablePrefix, mysql_real_escape_string($temp["PrimaryKey"], $this->dbConn));
		$result2 = mysql_query($query2, $this->dbConn);
		
		while($temp = mysql_fetch_assoc($result2))
		{
			$query3 = sprintf("SELECT * FROM %sposts WHERE PrimaryKey='%s'", $this->tablePrefix, mysql_real_escape_string($temp["PostID"], $this->dbConn));
			$result3 = mysql_query($query3, $this->dbConn);
			
			array_push($return, mysql_fetch_assoc($result3));
		}
		
		return $return;
		
	}
	
	public function getCategoryOrTag($ID, $type)
	{
		$query = sprintf("SELECT * FROM %scatstags WHERE TYPE='%s' AND PrimaryKey='%s'", $this->tablePrefix, mysql_real_escape_string($type, $this->dbConn), mysql_real_escape_string($ID, $this->dbConn));
		//echo $query;
		$result = mysql_query($query, $this->dbConn);
		
		$return = array();
		
		while($temp = mysql_fetch_assoc($result))
		{
			array_push($return, $temp);
		}
		
		return $return;
	}
	
	public function addPost($title, $data, $uri, $author, $date, $category, $tags)
	{
		
	}
	
}


?>