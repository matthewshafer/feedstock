<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief using mysqli which is a part of php5.  Uses things like multi_query.
 * 
 */
class database
{
	protected $dbConn = null;
	protected $tablePrefix = null;
	protected $connError = null;
	protected $checkedCategoryOrTag = null;
	protected $haveNext = false;
	public $debugQueries = array();
	public $queries = 0;
	
	public function __construct($username, $password, $serverAddress, $dbname, $tablePrefix)
	{
		$this->dbConn = new mysqli($serverAddress, $username, $password, $dbname);
		
		/*
		// Note that php 5.2.9 or 5.3.0 is needed for this to work or else it won't tell us when there is a failure
		if($this->dbConn->connect_error)
		{
			$this->connError = 'Connect Error (' . $this->dbConn->connect_errorno . ') ' . $this->dbConn->connect_error;
		}
		else
		{
			$this->tablePrefix = $this->dbConn->real_escape_string($tablePrefix);
		}
		
		*/
		
		if(mysqli_connect_error())
		{
			$this->connError = 'Unable to connect to the Database Server';
		}
		else
		{
			$this->tablePrefix = $this->dbConn->real_escape_string($tablePrefix);
		}
	}
	
	public function haveConnError()
	{
		return $this->connError;
	}
	
	/*
	public function queryError()
	{
		return $this->dbConn->error;
	}
	*/
	
	public function closeConnection()
	{
		$this->dbConn->close();
	}
	
	public function getPosts($offset, $draft = false)
	{
		$return = array();
		
		if(!$draft)
		{
			$query = sprintf("SELECT * FROM %sposts WHERE Draft='0' order by Date DESC LIMIT 11 OFFSET %s", $this->tablePrefix, $this->dbConn->real_escape_string($offset));
			
			if(F_MYSQLSTOREQUERIES)
			{
				array_push($this->debugQueries, $query);
			}
			
			$this->queries++;
		}
		else
		{
			$query = sprintf("SELECT * FROM %sposts order by Date DESC LIMIT 11 OFFSET %s", $this->tablePrefix, $this->dbConn->real_escape_string($offset));
			$this->queries++;
		}
		
		if($result = $this->dbConn->query($query))
		{
			$tmpArr = array();
			$tmpAuthors = array();
			
			while($row = $result->fetch_assoc())
			{
				array_push($tmpArr, $row);
				
				if(!isset($tmpAuthors[$row["Author"]]))
				{
					$tmpAuthors[$row["Author"]] = $row["Author"];
				}
			}
			$result->close();
			
			if(count($tmpArr) == 11)
			{
				$this->haveNext = true;
				array_pop($tmpArr);
			}
			
			$return = $this->generateAuthors($tmpArr, $tmpAuthors);
		}
		
		return $return;
	}
	
	private function generateAuthors($postArr, $authorArr)
	{
		if($postArr != null && $authorArr != null)
		{
			$queryStr = implode(", ", $authorArr);
			unset($authorArr);
			$authorArr = array();
			
			if($queryStr != null)
			{
				$query = sprintf("SELECT id, DisplayName FROM %susers WHERE id IN (%s)", $this->tablePrefix, $this->dbConn->real_escape_string($queryStr));
				
				if(F_MYSQLSTOREQUERIES)
				{
					array_push($this->debugQueries, $query);
				}
		
				$this->queries++;
			
				
				if($result = $this->dbConn->query($query))
				{
					while($row = $result->fetch_assoc())
					{
						$authorArr[$row["id"]] = $row["DisplayName"];
					}
					
					$result->close();
				}
			}
			
			$tmpCt = count($postArr);
			
			for($i = 0; $i < $tmpCt; $i++)
			{
				$id = $postArr[$i]["Author"];
			
				if(isset($authorArr[$id]))
				{
					$postArr[$i]["Author"] = $authorArr[$id];
				}
				else
				{
					$postArr[$i]["Author"] = "Unknown";
				}
			}
			
		}
		unset($authorArr);
		
		return $postArr;
	}
	
	public function getPage($URI, $draft = false)
	{
		$return = array();
		
		if(!$draft)
		{
			$query = sprintf("SELECT * FROM %spages WHERE URI='/%s' AND Draft='0'", $this->tablePrefix, $this->dbConn->real_escape_string($URI));
			
			if(F_MYSQLSTOREQUERIES)
			{
				array_push($this->debugQueries, $query);
			}
			
			$this->queries++;
		}
		else
		{
			$query = sprintf("SELECT * FROM %spages WHERE URI='/%s'", $this->tablePrefix, $this->dbConn->real_escape_string($URI));
		}
		
		if($result = $this->dbConn->query($query))
		{
			array_push($return, $result->fetch_assoc());
			
			$result->close();
		}
		
		return $return;
	}
	
	public function getSinglePost($URI, $draft = false)
	{
		$tmpArr = array();
		$authorArr = array();
		
		if(!$draft)
		{
			$query = sprintf("SELECT * FROM %sposts WHERE URI='/%s' AND Draft='0'", $this->tablePrefix, $this->dbConn->real_escape_string($URI));
			
			if(F_MYSQLSTOREQUERIES)
			{
				array_push($this->debugQueries, $query);
			}
			$this->queries++;
		}
		else
		{
			$query = sprintf("SELECT * FROM %sposts WHERE URI='/%s'", $this->tablePrefix, $this->dbConn->real_escape_string($URI));
		}
		
		if($result = $this->dbConn->query($query))
		{
			$row = $result->fetch_assoc();
			array_push($tmpArr, $row);
			
			if(isset($row["Author"]))
			{
				array_push($authorArr, $row["Author"]);
			}
			
			$result->close();
		}
		
		$return = $this->generateAuthors($tmpArr, $authorArr);
		
		if(isset($return[0]["PostData"]))
		{
			$return[0]["PostData"] = stripslashes($return[0]["PostData"]);
		}
		
		return $return;
	}
	
	public function getPostCategoryOrTag($IdArray, $type)
	{
		$tmpCt = null;
		static $arrayWithPostTax = null;
		static $queryStr = null;
		$return = array();
		
		if($arrayWithPostTax == null)
		{
			$tmpArr = array();
			$arrayWithPostTax = array();
			$query = null;
		
			if($tmpCt == null)
			{
				$tmpCt = count($IdArray);
			}
		
			for($i = 0; $i < $tmpCt; $i++)
			{
				$query .= sprintf("SELECT * FROM %sposts_tax WHERE PostID='%s';", $this->tablePrefix, $this->dbConn->real_escape_string($IdArray[$i]));
			}
			
			if(F_MYSQLSTOREQUERIES)
			{
				array_push($this->debugQueries, $query);
			}
			//echo $query;
			$this->queries++;
			
			if($this->dbConn->multi_query($query))
			{
				do
				{
					if($result = $this->dbConn->store_result())
					{
						while($row = $result->fetch_assoc())
						{
							if(isset($arrayWithPostTax[$row["PostID"]]))
							{
								array_push($arrayWithPostTax[$row["PostID"]], $row["CatTagID"]);
								
								if(!isset($tmpArr[$row["CatTagID"]]))
								{
									$tmpArr[$row["CatTagID"]] = $row["CatTagID"];
								}
							}
							else
							{
								$arrayWithPostTax[$row["PostID"]] = array();
								array_push($arrayWithPostTax[$row["PostID"]], $row["CatTagID"]);
								
								if(!isset($tmpArr[$row["CatTagID"]]))
								{
									$tmpArr[$row["CatTagID"]] = $row["CatTagID"];
								}
							}
						}
						$result->close();
					}
				} while($this->dbConn->next_result());
			}
			
			$queryStr = implode(", ", $tmpArr);
		}
		
		if($queryStr != null)
		{
			$catTagResultArr = array();
			$taxArr = $arrayWithPostTax;
			
			if($type == 'tag')
			{
				$query = sprintf("SELECT * FROM %scatstags WHERE Type='1' AND PrimaryKey IN (%s)", $this->tablePrefix, $this->dbConn->real_escape_string($queryStr));
				
				if(F_MYSQLSTOREQUERIES)
				{
					array_push($this->debugQueries, $query);
				}
			
				$this->queries++;
			}
			else
			{
				$query = sprintf("SELECT * FROM %scatstags WHERE Type='0' AND PrimaryKey IN (%s)", $this->tablePrefix, $this->dbConn->real_escape_string($queryStr));
				
				if(F_MYSQLSTOREQUERIES)
				{
					array_push($this->debugQueries, $query);
				}
			
				$this->queries++;
			}
			
			if($result = $this->dbConn->query($query))
			{
				while($row = $result->fetch_assoc())
				{
					$catTagResultArr[$row["PrimaryKey"]] = $row;
				}
								
				$result->close();
			}
			
			while($tmp = each($taxArr))
			{
				$srsTemp = array();
				
				while($tmp2 = each($tmp["value"]))
				{
					if(isset($catTagResultArr[$tmp2["value"]]))
					{
						$srsTemp[$tmp2["key"]] = $catTagResultArr[$tmp2["value"]];
					}
				}
				
				$return[$tmp["key"]] = $srsTemp;
			}
		}
		
		return $return;
	}
	
	public function getPostsInCategoryOrTag($URIName, $type, $draft = false)
	{
		$return = array();
		
		$query = sprintf("SELECT PostID FROM %sposts_tax WHERE CatTagID='%s'", $this->tablePrefix, $this->dbConn->real_escape_string($this->checkedCategoryOrTag["PrimaryKey"]));
		
		if(F_MYSQLSTOREQUERIES)
		{
			array_push($this->debugQueries, $query);
		}
			
		$this->queries++;
		
		$tmpArr = array();
		
		if($result = $this->dbConn->query($query))
		{
			while($row = $result->fetch_assoc())
			{
				array_push($tmpArr, $row["PostID"]);
			}
			
			$result->close();
			
			$queryStr = implode(", ", $tmpArr);
			
			if($queryStr != null)
			{
				if(!$draft)
				{
					$query = sprintf("SELECT * FROM %sposts WHERE Draft='0' AND PrimaryKey IN (%s)", $this->tablePrefix, $this->dbConn->real_escape_string($queryStr));
				}
				else
				{
					$query = sprintf("SELECT * FROM %sposts WHERE PrimaryKey IN (%s)", $this->tablePrefix, $this->dbConn->real_escape_string($queryStr));
				}
				
				if(F_MYSQLSTOREQUERIES)
				{
					array_push($this->debugQueries, $query);
				}
		
				$this->queries++;
			
				if($result = $this->dbConn->query($query))
				{
					$tmpAuthor = array();
					while($row = $result->fetch_assoc())
					{
						if(!isset($tmpAuthor[$row["Author"]]))
						{
							$tmpAuthor[$row["Author"]] = $row["Author"];
						}
						
						array_push($return, $row);
					}
					$result->close();
					
					$return = $this->generateAuthors($return, $tmpAuthor);
					unset($tmpAuthor);
				}
			}
		}
		return $return;
	}
	
	public function getCategoryOrTag($ID, $type)
	{
		
	}
	
	public function checkCategoryTagName($name, $type)
	{
		$return = false;
		
		$query = sprintf("SELECT * FROM %scatstags WHERE URIName='%s' AND Type='%s'", $this->tablePrefix, $this->dbConn->real_escape_string($name), $this->dbConn->real_escape_string($type));
		
		if(F_MYSQLSTOREQUERIES)
		{
			array_push($this->debugQueries, $query);
		}
			
		$this->queries++;
		
		if($result = $this->dbConn->query($query))
		{
			if($row = $result->fetch_assoc())
			{
				$return = true;
				
				$this->checkedCategoryOrTag = $row;
			}
			
			$result->close();
		}
		
		return $return;
	}
	
	public function listCategoriesOrTags($type)
	{
		return array();
	}
	
	public function haveNextPage()
	{
		return $this->haveNext;
	}

}
?>