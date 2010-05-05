<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief using mysqli which is a part of php5.  Uses things like multi_query.
 * 
 */
class mysqliDatabase
{
	protected $dbConn = null;
	protected $tablePrefix = null;
	protected $connError = null;
	protected $checkedCategoryOrTag = null;
	protected $haveNext = false;
	protected $cacher = null;
	protected $haveCacher = false;
	public $debugQueries = array();
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
	public function __construct($username, $password, $serverAddress, $dbname, $tablePrefix, $cacher = null)
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
		
		$this->cacher = $cacher;
		
		if($this->cacher != null)
		{
			$this->haveCacher = true;
		}
		
		
		if(mysqli_connect_error())
		{
			$this->connError = 'Unable to connect to the Database Server';
		}
		else
		{
			$this->tablePrefix = $this->dbConn->real_escape_string($tablePrefix);
		}
	}
	
	/**
	 * haveConnError function.
	 * 
	 * @access public
	 * @return void
	 */
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
	
	/**
	 * closeConnection function.
	 * 
	 * @access public
	 * @return void
	 */
	public function closeConnection()
	{
		$this->dbConn->close();
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
		
		
		if($this->haveCacher && $this->cacher->checkExists(sprintf("%s%d", $query, 1)) && $this->cacher->checkExists($query))
		{
			$return = $this->cacher->getCachedData();
			$this->cacher->checkExists(sprintf("%s%d", $query, 1));
			$this->haveNext = $this->cacher->getCachedData();
		}
		else if($result = $this->dbConn->query($query))
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
			//print_r($tmpAuthors);
			$return = $this->generateAuthors($tmpArr, $tmpAuthors);
			
			if($this->haveCacher)
			{
				//echo "here";
				$this->cacher->writeCachedFile(sprintf("%s%d", $query, 1), $this->haveNext);
				$this->cacher->writeCachedFile($query, $return);
			}
		}
		
		return $return;
	}
	
	/**
	 * generateAuthors function.
	 * 
	 * @access private
	 * @param mixed $postArr
	 * @param mixed $authorArr
	 * @return void
	 */
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
	
	/**
	 * getPage function.
	 * 
	 * @access public
	 * @param mixed $URI
	 * @param bool $draft. (default: false)
	 * @return void
	 */
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
		if($this->haveCacher && $this->cacher->checkExists($query))
		{
			$return = $this->cacher->getCachedData();
		}
		else if($result = $this->dbConn->query($query))
		{
			$tmp = $result->fetch_assoc();
			if(!empty($tmp))
			{
				array_push($return, $tmp);
			}
			
			if($this->haveCacher)
			{
				$this->cacher->writeCachedFile($query, $return);
			}
			$result->close();
		}
		
		return $return;
	}
	
	/**
	 * getSinglePost function.
	 * 
	 * @access public
	 * @param mixed $URI
	 * @param bool $draft. (default: false)
	 * @return void
	 */
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
		
		if($this->haveCacher && $this->cacher->checkExists($query))
		{
			$tmpArr = $this->cacher->getCachedData();
			
			if(isset($tmpArr[0]["Author"]))
			{
				array_push($authorArr, $tmpArr[0]["Author"]);
			}
		}
		else if($result = $this->dbConn->query($query))
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
	
	/**
	 * getPostCategoryOrTag function.
	 * 
	 * @access public
	 * @param mixed $IdArray
	 * @param mixed $type
	 * @return void
	 */
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
			
			$query1 = sprintf("%s%d", $query, 1);
			$query2 = sprintf("%s%d", $query, 2);
			
			if($this->haveCacher && $this->cacher->checkExists($query1) && $this->cacher->checkExists($query2))
			{
				//echo "cat or tag";
				if($queryStr == null)
				{
					$queryStr = $this->cacher->getCachedData();
				}
				$this->cacher->checkExists($query1);
				$arrayWithPostTax = $this->cacher->getCachedData();
			}
			else if($this->dbConn->multi_query($query))
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
				
				$queryStr = implode(", ", $tmpArr);
				
				
				if($this->haveCacher)
				{	
					//echo "write me";
					$this->cacher->writeCachedFile($query2, $queryStr);
					$this->cacher->writeCachedFile($query1, $arrayWithPostTax);
				}
			}
			
			
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
			
			if($this->haveCacher && $this->cacher->checkExists($query))
			{
				$return = $this->cacher->getCachedData();
			}
			else if($result = $this->dbConn->query($query))
			{
				while($row = $result->fetch_assoc())
				{
					$catTagResultArr[$row["PrimaryKey"]] = $row;
				}
						
				$result->close();
			
			
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
				
				if($this->haveCacher)
				{
					$this->cacher->writeCachedFile($query, $return);
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * getPostsInCategoryOrTag function.
	 * 
	 * @access public
	 * @param mixed $URIName
	 * @param mixed $type
	 * @param bool $draft. (default: false)
	 * @return void
	 */
	public function getPostsInCategoryOrTag($URIName, $type, $offset, $draft = false)
	{
		$return = array();
		$queryStr = null;
		
		$query = sprintf("SELECT PostID FROM %sposts_tax WHERE CatTagID='%s'", $this->tablePrefix, $this->dbConn->real_escape_string($this->checkedCategoryOrTag["PrimaryKey"]));
		
		if(F_MYSQLSTOREQUERIES)
		{
			array_push($this->debugQueries, $query);
		}
			
		$this->queries++;
		
		$tmpArr = array();
		
		
		if($this->haveCacher && $this->cacher->checkExists(sprintf("%s%d", $query, 1)) && $this->cacher->checkExists($query))
		{
			$queryStr = $this->cacher->getCachedData();
			$this->cacher->checkExists(sprintf("%s%d", $query, 1));
			$this->haveNext = $this->cacher->getCachedData();
		}
		else if($result = $this->dbConn->query($query))
		{
			while($row = $result->fetch_assoc())
			{
				array_push($tmpArr, $row["PostID"]);
			}
			
			$result->close();
			
			$queryStr = implode(", ", $tmpArr);
			
			if($this->haveCacher)
			{
				echo $query;
				$this->cacher->writeCachedFile($query, $queryStr);
			}
		}
		
		
		if($queryStr != null)
		{
			if(!$draft)
			{
				$query = sprintf("SELECT * FROM %sposts WHERE Draft='0' AND PrimaryKey IN (%s) ORDER BY Date DESC LIMIT 11 OFFSET %d", $this->tablePrefix, $this->dbConn->real_escape_string($queryStr), $offset);
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
			
			if($this->haveCacher && $this->cacher->checkExists($query))
			{
				$return = $this->cacher->getCachedData();
				//echo "\nhere\n";
			}
			else if($result = $this->dbConn->query($query))
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
				
				$tmpCt = count($return);
				if($tmpCt > 10)
				{
					$this->haveNext = true;
					array_pop($return);
				}
				unset($tmpAuthor);
				
				if($this->haveCacher)
				{
					$this->cacher->writeCachedFile($query, $return);
					$this->cacher->writeCachedFile(sprintf("%s%d", $query, 1), $this->haveNext);
				}
			}
		}
		return $return;
	}
	
	public function getCategoryOrTag($ID, $type)
	{
		
	}
	
	/**
	 * checkCategoryTagName function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @param mixed $type
	 * @return void
	 */
	public function checkCategoryTagName($name, $type)
	{
		$return = false;
		
		$query = sprintf("SELECT * FROM %scatstags WHERE URIName='%s' AND Type='%s'", $this->tablePrefix, $this->dbConn->real_escape_string($name), $this->dbConn->real_escape_string($type));
		
		if(F_MYSQLSTOREQUERIES)
		{
			array_push($this->debugQueries, $query);
		}
			
		$this->queries++;
		
		if($this->haveCacher && $this->cacher->checkExists($query))
		{
			$tmp = $this->cacher->getCachedData();
			
			if($tmp != null)
			{
				$return = true;
				$this->checkedCategoryOrTag = $tmp;
			}
			//$this->checkedCategoryOrTag = $this->cacher->getCachedData();
		}
		else if($result = $this->dbConn->query($query))
		{
			if($row = $result->fetch_assoc())
			{
				$return = true;
				
				$this->checkedCategoryOrTag = $row;
			}
			
			$result->close();
			
			if($this->haveCacher)
			{
				$this->cacher->writeCachedFile($query, $this->checkedCategoryOrTag);
			}
		}
		
		return $return;
	}
	
	/**
	 * listCategoriesOrTags function.
	 * 
	 * @access public
	 * @param mixed $type
	 * @return void
	 */
	public function listCategoriesOrTags($type)
	{
		$return = array();
		// can be rewritten possibly to use prepared statements
		$query = sprintf("SELECT * FROM %scatstags WHERE Type='%s'", $this->tablePrefix, $this->dbConn->real_escape_string($type));
		
		if($this->haveCacher && $this->cacher->checkExists($query))
		{
			$return = $this->cacher->getCachedData();
		}
		else if($result = $this->dbConn->query($query))
		{
			while($row = $result->fetch_assoc())
			{
				array_push($return, $row);
			}
			
			$result->close();
			
			if($this->haveCacher)
			{
				$this->cacher->writeCachedFile($query, $return);
			}
		}
		
		return $return;
	}
	
	/**
	 * haveNextPage function.
	 * 
	 * @access public
	 * @return void
	 */
	public function haveNextPage()
	{
		return $this->haveNext;
	}
	
	/**
	 * getCorralByName function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @return void
	 */
	public function getCorralByName($name)
	{
		$return = array();
		$formattedQuery = sprintf("SELECT Title, URI FROM %spages WHERE Corral=?", $this->tablePrefix);
		$cacheQuery = sprintf("%s%s", $formattedQuery, $name);
		
		
		if($this->haveCacher && $this->cacher->checkExists($cacheQuery))
		{
			$return = $this->cacher->getCachedData();
		}
		else
		{
			$query = $this->dbConn->prepare($formattedQuery);
			$query->bind_param('s', $name);
			$query->execute();
			$query->store_result();
			$query->bind_result($title, $uri);
			
			$count = 0;
			
			//im thinking we create our own associative array, its pretty easy to do
			while($query->fetch())
			{
				$return[$count] = array();
				$return[$count]["Title"] = $title;
				$return[$count]["URI"] = $uri;
				$count++;
			}
			$query->close();
			
			if($this->haveCacher)
			{
				$this->cacher->writeCachedFile($cacheQuery, $return);
			}
		}
		
		return $return;
	}
	
	/**
	 * getSnippetByName function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @return void
	 */
	public function getSnippetByName($name)
	{
		$return = array();
		$formattedQuery = sprintf("SELECT SnippetData FROM %ssnippet WHERE Name=? LIMIT 1", $this->tablePrefix);
		$cacheQuery = sprintf("%s%s", $formattedQuery, $name);
		
		if($this->haveCacher && $this->cacher->checkExists($cacheQuery))
		{
			$return = $this->cacher->getCachedData();
		}
		else
		{
			$query = $this->dbConn->prepare($formattedQuery);
			$query->bind_param('s', $name);
			$query->execute();
			$query->store_result();
			$query->bind_result($data);
			
			if($query->fetch())
			{
				$return["SnippetData"] = $data;
			}
			
			if($this->haveCacher)
			{
				$this->cacher->writeCachedFile($cacheQuery, $return);
			}
			
			$query->close();
		}
		
		/*
		$query = sprintf("SELECT SnippetData FROM %ssnippet WHERE Name='%s'", $this->tablePrefix, $name);
		
		if($result = $this->dbConn->query($query))
		{
			if($row = $result->fetch_assoc())
			{
				$return = $row;
			}
			$result->close();
		}
		*/
		return $return;
	}

}
?>