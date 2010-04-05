<?php
/**
* @file
* @author Matthew Shafer <matt@niftystopwatch.com>
* @brief standard mysql class
* 
*/
class database
{
	protected $dbConn = null;
	protected $dBaseValid = null;
	protected $tablePrefix = null;
	protected $connError = null;
	public $queries = 0;
	
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
		$this->dbConn = mysql_connect($serverAddress, $username, $password);
		
		if(!is_resource($this->dbConn))
		{
			//die("Unable to connect to the Database Server");
			$this->connError = "Unable to connect to the Database Server";
		}
		if($this->connError == null)
		{
			$this->dBaseValid = mysql_select_db($dbname, $this->dbConn);
		
			if(!$this->dBaseValid)
			{
				//die("Unable to connect to the database");
				$this->connError = "Unable to connect to the database";
			}
		
			$this->tablePrefix = mysql_real_escape_string($tablePrefix, $this->dbConn);
		}
	}
	
	public function haveConnError()
	{
		return $this->connError;
	}
	
	/**
	 * closeConnection function.
	 * 
	 * @access public
	 * @return void
	 */
	public function closeConnection()
	{
		mysql_close($this->dbConn);
	}
	
	/**
	 * getPosts function.
	 * 
	 * @access public
	 * @param mixed $offset
	 * @param bool $draft. (default: false)
	 * @return void
	 */
	public function getPosts($offset, $draft = false)
	{
		$authorArr = array();
		
		if(!$draft)
		{
			$query = sprintf("SELECT * FROM %sposts WHERE Draft='0' order by Date DESC LIMIT 10 OFFSET %s", $this->tablePrefix, mysql_real_escape_string($offset, $this->dbConn));
			$this->queries++;
		}
		else
		{
			$query = sprintf("SELECT * FROM %sposts order by Date DESC LIMIT 10 OFFSET %s", $this->tablePrefix, mysql_real_escape_string($offset, $this->dbConn));
			$this->queries++;
		}
		
		$result = mysql_query($query, $this->dbConn);
		
		$return = array();
		// need to check if anything is in the array
		
		while($temp = mysql_fetch_assoc($result))
		{
			
			if(isset($authorArr[$temp["Author"]]))
			{
				$temp["Author"] = $authorArr[$temp["Author"]];
			}
			else
			{
				$query = sprintf("SELECT DisplayName FROM %susers WHERE id='%s' LIMIT 1", $this->tablePrefix, mysql_real_escape_string($temp["Author"], $this->dbConn));
				$this->queries++;
				$result2 = mysql_query($query, $this->dbConn);
				$tmpArr = mysql_fetch_assoc($result2);
				
				if(isset($tmpArr["DisplayName"]))
				{
					$authorArr[$temp["Author"]] = $tmpArr["DisplayName"];
					$temp["Author"] = $authorArr[$temp["Author"]];
				}
			}
			
			if(isset($temp["PostData"]))
			{
				$temp["PostData"] = stripslashes($temp["PostData"]);
			}
			
			array_push($return, $temp);
		}
		
		//print_r($return);
		return $return;
	}
	
	public function getPage($URI, $draft = false)
	{
		if(!$draft)
		{
			$query = sprintf("SELECT * FROM %spages WHERE URI='/%s' AND Draft='0'", $this->tablePrefix, mysql_real_escape_string($URI, $this->dbConn));
			$this->queries++;
		}
		else
		{
			$query = sprintf("SELECT * FROM %spages WHERE URI='/%s'", $this->tablePrefix, mysql_real_escape_string($URI, $this->dbConn));
			$this->queries++;
		}
		//echo $query;
		$result = mysql_query($query , $this->dbConn);
		
		$return = array();
		
		while($temp = mysql_fetch_assoc($result))
		{
			array_push($return, $temp);
		}

		
		return $return;
	}
	
	public function getSinglePost($URI, $draft = false)
	{
		if(!$draft)
		{
			$query = sprintf("SELECT * FROM %sposts WHERE URI='/%s' AND Draft='0'", $this->tablePrefix, mysql_real_escape_string($URI, $this->dbConn));
			$this->queries++;
		}
		else
		{
			$query = sprintf("SELECT * FROM %sposts WHERE URI='/%s'", $this->tablePrefix, mysql_real_escape_string($URI, $this->dbConn));
			$this->queries++;
		}
		
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
		$this->queries++;
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
		$this->queries++;
		$result = mysql_query($query, $this->dbConn);
		
		$return = array();
		
		while($temp = mysql_fetch_assoc($result))
		{
			array_push($return, $temp);
		}
		
		return $return;
	}
	
	
	public function getPostCategoryOrTag($IdArray, $type)
	{
		$tmpArr = array();
		$tmpArr2 = array();
		$tmpArr3 = array();
		$queryStr = null;
		static $arrWithPostTax = null;
		static $queryStr;
		
		if($arrWithPostTax == null)
		{
			foreach($IdArray as $key)
			{
				$query = sprintf("SELECT CatTagID FROM %sposts_tax WHERE PostID='%s'", $this->tablePrefix, mysql_real_escape_string($key, $this->dbConn));
				$this->queries++;
				$result = mysql_query($query, $this->dbConn);
			
				$tmpArr[$key] = array();
			
				while($tmp = mysql_fetch_assoc($result))
				{
					if(!isset($tmpArr2[$tmp["CatTagID"]]))
					{
						$tmpArr2[$tmp["CatTagID"]] = $tmp["CatTagID"];
					}
					array_push($tmpArr[$key], $tmp["CatTagID"]);
				}
			}
		
			// store tmpArr so if we do cats we already have the values
			$arrWithPostTax = $tmpArr;
			$queryStr = implode(", ", $tmpArr2);
		}
		else
		{
			$tmpArr = $arrWithPostTax;
		}
		
		//echo "QueryStr: " . $queryStr;
		if($queryStr != null)
		{
			if($type == "tag")
			{
				$query = sprintf("SELECT * FROM %scatstags WHERE Type='1' AND PrimaryKey IN (%s)", $this->tablePrefix, mysql_real_escape_string($queryStr, $this->dbConn));
				$this->queries++;
			}
			else
			{
				$query = sprintf("SELECT * FROM %scatstags WHERE Type='0' AND PrimaryKey IN (%s)", $this->tablePrefix, mysql_real_escape_string($queryStr, $this->dbConn));
				//echo $query;
				$this->queries++;
			}
		
			$result = mysql_query($query, $this->dbConn);
			
			while($tmp = mysql_fetch_assoc($result))
			{
				$tmpArr3[$tmp["PrimaryKey"]] = $tmp;
			}
		
			//print_r($tmpArr3);
			//print_r($tmpArr);
			
			while($tmp = each($tmpArr))
			{
				$srsTemp = array();
				while($tmp2 = each($tmp["value"]))
				{
					if(isset($tmpArr3[$tmp2["value"]]))
					{
						$srsTemp[$tmp2["key"]] = $tmpArr3[$tmp2["value"]];
					}
				}
			
				$tmpArr[$tmp["key"]] = $srsTemp;
			}
		}
		//print_r($tmpArr);
		return $tmpArr;
	}
	
	// still need to finish the getting the posts part.
	public function getPostsInCategoryOrTag($URIName, $type, $draft = false)
	{
		$query = sprintf("SELECT PrimaryKey FROM %scatstags WHERE URIName='%s' AND Type='%s'", $this->tablePrefix, mysql_real_escape_string($URIName, $this->dbConn), mysql_real_escape_string($type, $this->dbConn));
		$this->queries++;
		$result = mysql_query($query, $this->dbConn);
		
		$return = array();
		
		$result2 = null;
		
		$temp = mysql_fetch_assoc($result);
		
		//print_r($temp);
		
		$query2 = sprintf("SELECT PostID FROM %sposts_tax WHERE CatTagID='%s'", $this->tablePrefix, mysql_real_escape_string($temp["PrimaryKey"], $this->dbConn));
		$this->queries++;
		$result2 = mysql_query($query2, $this->dbConn);
		
		while($temp = mysql_fetch_assoc($result2))
		{
			if(!$draft)
			{
				$query3 = sprintf("SELECT * FROM %sposts WHERE PrimaryKey='%s' AND Draft='0'", $this->tablePrefix, mysql_real_escape_string($temp["PostID"], $this->dbConn));
				$this->queries++;
			}
			else
			{
				$query3 = sprintf("SELECT * FROM %sposts WHERE PrimaryKey='%s'", $this->tablePrefix, mysql_real_escape_string($temp["PostID"], $this->dbConn));
				$this->queries++;
			}
			$result3 = mysql_query($query3, $this->dbConn);
			
			array_push($return, mysql_fetch_assoc($result3));
		}
		
		return $return;
		
	}
	
	public function getCategoryOrTag($ID, $type)
	{
		$query = sprintf("SELECT * FROM %scatstags WHERE TYPE='%s' AND PrimaryKey='%s'", $this->tablePrefix, mysql_real_escape_string($type, $this->dbConn), mysql_real_escape_string($ID, $this->dbConn));
		$this->queries++;
		//echo $query;
		$result = mysql_query($query, $this->dbConn);
		
		$return = array();
		
		while($temp = mysql_fetch_assoc($result))
		{
			array_push($return, $temp);
		}
		
		return $return;
	}
	
	public function corralPage($ID)
	{
		$query = sprintf("SELECT * FROM %spages WHERE Corral='%s' AND Draft='0'", $this->tablePrefix, mysql_real_escape_string($ID, $this->dbConn));
		$this->queries++;
		$result = mysql_query($query, $this->dbConn);
		
		$return = array();
		
		while($temp = mysql_fetch_assoc($result))
		{
			array_push($return, $temp);
		}
		
		return $return;
	}
	
}


?>