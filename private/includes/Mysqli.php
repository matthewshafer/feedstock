<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief using mysqli which is a part of php5.  Uses things like multi_query.
 * 
 */
class MysqliDatabase
{
	protected $databaseConnection = null;
	protected $tablePrefix = null;
	protected $connectionError = null;
	protected $checkedCategoryOrTag = null;
	protected $haveNextPage = false;
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
	 * @param mixed $databaseName
	 * @param mixed $tablePrefix
	 * @return void
	 */
	public function __construct($username, $password, $serverAddress, $databaseName, $tablePrefix, $cacher = null)
	{
		$this->databaseConnection = new mysqli($serverAddress, $username, $password, $databaseName);
		
		/*
		// Note that php 5.2.9 or 5.3.0 is needed for this to work or else it won't tell us when there is a failure
		if($this->databaseConnection->connect_error)
		{
			$this->connectionError = 'Connect Error (' . $this->databaseConnection->connect_errorno . ') ' . $this->databaseConnection->connect_error;
		}
		else
		{
			$this->tablePrefix = $this->databaseConnection->real_escape_string($tablePrefix);
		}
		
		*/
		
		$this->cacher = $cacher;
		
		if($this->cacher != null)
		{
			$this->haveCacher = true;
		}
		
		
		if(mysqli_connect_error())
		{
			$this->connectionError = 'Unable to connect to the Database Server';
		}
		else
		{
			$this->tablePrefix = $this->databaseConnection->real_escape_string($tablePrefix);
		}
	}
	
	/**
	 * haveConnectionError function.
	 * 
	 * @access public
	 * @return void
	 */
	public function haveConnectionError()
	{
		return $this->connectionError;
	}
	
	/*
	public function queryError()
	{
		return $this->databaseConnection->error;
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
		$this->databaseConnection->close();
	}
	
	/**
	 * getPosts function.
	 * 
	 * @access public
	 * @param mixed $offset
	 * @param bool $draft. (default: false)
	 * @return void
	 */
	public function getPosts($limit, $offset, $draft = false)
	{
		$return = array();
		
		$limit = $limit + 1;
		
		if(!$draft)
		{
			//$query = sprintf("SELECT * FROM %sposts WHERE Draft='0' order by Date DESC LIMIT %s OFFSET %s", $this->tablePrefix, $this->databaseConnection->real_escape_string($limit), $this->databaseConnection->real_escape_string($offset));
			
			$query = sprintf("SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author WHERE Draft='0' ORDER BY Date DESC LIMIT %s OFFSET %s", $this->tablePrefix, $this->tablePrefix, $this->databaseConnection->real_escape_string($limit), $this->databaseConnection->real_escape_string($offset));
			
			if(F_MYSQLSTOREQUERIES)
			{
				array_push($this->debugQueries, $query);
			}
			
			$this->queries++;
		}
		else
		{
			//$query = sprintf("SELECT * FROM %sposts order by Date DESC LIMIT %s OFFSET %s", $this->tablePrefix, $this->databaseConnection->real_escape_string($limit), $this->databaseConnection->real_escape_string($offset));
			
			$query = sprintf("SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author  ORDER BY Date DESC LIMIT %s OFFSET %s", $this->tablePrefix, $this->tablePrefix, $this->databaseConnection->real_escape_string($limit), $this->databaseConnection->real_escape_string($offset));
			$this->queries++;
		}
		
		
		if($this->haveCacher && $this->cacher->checkExists(sprintf("%s%d", $query, 1)) && $this->cacher->checkExists($query))
		{
			$return = $this->cacher->getCachedData();
			$this->haveNextPage = $this->cacher->getCachedData();
		}
		else if($result = $this->databaseConnection->query($query))
		{
			$tempArray = array();
			
			while($row = $result->fetch_assoc())
			{
				if($row["Author"] == null)
				{
					$row["Author"] = "Unknown";
				}
				array_push($tempArray, $row);
			}
			$result->close();
			
			if(count($tempArray) == ($limit))
			{
				$this->haveNextPage = true;
				array_pop($tempArray);
			}
			//print_r($tempAuthors);
			$return = $tempArray;
			
			if($this->haveCacher)
			{
				//echo "here";
				$this->cacher->writeCachedFile(sprintf("%s%d", $query, 1), $this->haveNextPage);
				$this->cacher->writeCachedFile($query, $return);
			}
		}
		
		return $return;
	}
	
	
	/**
	 * getPage function.
	 * 
	 * @access public
	 * @param mixed $uri
	 * @param bool $draft. (default: false)
	 * @return void
	 */
	public function getPage($uri, $draft = false)
	{
		$return = array();
		
		if(!$draft)
		{
			$query = sprintf("SELECT * FROM %spages WHERE URI='/%s' AND Draft='0'", $this->tablePrefix, $this->databaseConnection->real_escape_string($uri));
			
			if(F_MYSQLSTOREQUERIES)
			{
				array_push($this->debugQueries, $query);
			}
			
			$this->queries++;
		}
		else
		{
			$query = sprintf("SELECT * FROM %spages WHERE URI='/%s'", $this->tablePrefix, $this->databaseConnection->real_escape_string($uri));
		}
		if($this->haveCacher && $this->cacher->checkExists($query))
		{
			$return = $this->cacher->getCachedData();
		}
		else if($result = $this->databaseConnection->query($query))
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
	 * @param mixed $uri
	 * @param bool $draft. (default: false)
	 * @return void
	 */
	public function getSinglePost($uri, $draft = false)
	{
		$tempArray = array();
		
		if(!$draft)
		{
			$query = sprintf("SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author WHERE URI='/%s' AND Draft='0'", $this->tablePrefix, $this->tablePrefix, $this->databaseConnection->real_escape_string($uri));
			
			if(F_MYSQLSTOREQUERIES)
			{
				array_push($this->debugQueries, $query);
			}
			$this->queries++;
		}
		else
		{
			$query = sprintf("SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author WHERE URI='/%s'", $this->tablePrefix, $this->tablePrefix, $this->databaseConnection->real_escape_string($uri));
		}
		
		if($this->haveCacher && $this->cacher->checkExists($query))
		{
			$tempArray = $this->cacher->getCachedData();
			
		}
		else if($result = $this->databaseConnection->query($query))
		{
			$row = $result->fetch_assoc();
			
			if($row["Author"] == null)
			{
				$row["Author"] = "Unknown";
			}
			
			array_push($tempArray, $row);
			
			$result->close();
			
			
			if($this->haveCacher)
			{
				$this->cacher->writeCachedFile($query, $tempArray);
			}
		}
		
		return $tempArray;
	}
	
	/**
	 * getPostCategoryOrTag function.
	 * 
	 * @access public
	 * @param mixed $idArray
	 * @param mixed $type
	 * @return void
	 */
	public function getPostCategoryOrTag($idArray, $type)
	{
		$idCount = null;
		static $arrayWithPostTax = null;
		static $queryString = null;
		$return = array();
		
		if($arrayWithPostTax == null)
		{
			$tempArray = array();
			$arrayWithPostTax = array();
			$query = null;
		
			if($idCount == null)
			{
				$idCount = count($idArray);
			}
		
			for($i = 0; $i < $idCount; $i++)
			{
				$query .= sprintf("SELECT * FROM %sposts_tax WHERE PostID='%s';", $this->tablePrefix, $this->databaseConnection->real_escape_string($idArray[$i]));
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
				if($queryString == null)
				{
					$queryString = $this->cacher->getCachedData();
				}
				$arrayWithPostTax = $this->cacher->getCachedData();
			}
			else if($this->databaseConnection->multi_query($query))
			{
				do
				{
					if($result = $this->databaseConnection->store_result())
					{
						while($row = $result->fetch_assoc())
						{
							if(isset($arrayWithPostTax[$row["PostID"]]))
							{
								array_push($arrayWithPostTax[$row["PostID"]], $row["CatTagID"]);
								
								if(!isset($tempArray[$row["CatTagID"]]))
								{
									$tempArray[$row["CatTagID"]] = $row["CatTagID"];
								}
							}
							else
							{
								$arrayWithPostTax[$row["PostID"]] = array();
								array_push($arrayWithPostTax[$row["PostID"]], $row["CatTagID"]);
								
								if(!isset($tempArray[$row["CatTagID"]]))
								{
									$tempArray[$row["CatTagID"]] = $row["CatTagID"];
								}
							}
						}
						$result->close();
					}
				} while($this->databaseConnection->next_result());
				
				$queryString = implode(", ", $tempArray);
				unset($tempArray);
				
				
				if($this->haveCacher)
				{	
					//echo "write me";
					$this->cacher->writeCachedFile($query2, $queryString);
					$this->cacher->writeCachedFile($query1, $arrayWithPostTax);
				}
			}
			
			
		}
		
		if($queryString != null)
		{
			$categoryTagResultArray = array();
			$taxArray = $arrayWithPostTax;
			
			if($type == 'tag')
			{
				$query = sprintf("SELECT * FROM %scatstags WHERE Type='1' AND PrimaryKey IN (%s)", $this->tablePrefix, $this->databaseConnection->real_escape_string($queryString));
				
				if(F_MYSQLSTOREQUERIES)
				{
					array_push($this->debugQueries, $query);
				}
			
				$this->queries++;
			}
			else
			{
				$query = sprintf("SELECT * FROM %scatstags WHERE Type='0' AND PrimaryKey IN (%s)", $this->tablePrefix, $this->databaseConnection->real_escape_string($queryString));
				
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
			else if($result = $this->databaseConnection->query($query))
			{
				while($row = $result->fetch_assoc())
				{
					$categoryTagResultArray[$row["PrimaryKey"]] = $row;
				}
						
				$result->close();
			
			
				while($temp = each($taxArray))
				{
					$temp2 = array();
					
					while($temp3 = each($temp["value"]))
					{
						if(isset($categoryTagResultArray[$temp3["value"]]))
						{
							$temp2[$temp3["key"]] = $categoryTagResultArray[$temp3["value"]];
						}
					}
					
					$return[$temp["key"]] = $temp2;
				}
				
				unset($categoryTagResultArray);
				
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
	 * @param mixed $uriName
	 * @param mixed $type
	 * @param bool $draft. (default: false)
	 * @return void
	 */
	public function getPostsInCategoryOrTag($type, $limit, $offset, $draft = false)
	{
		$return = array();
		$queryString = null;
		$limit = $limit + 1;
		
		// checking for valid categories or tags is done before we get here and we store that in a value so there is no need to check it again. thats why we use $this->checkedCategoryOrTag["PrimaryKey"]
		$query = sprintf("SELECT PostID FROM %sposts_tax WHERE CatTagID='%s'", $this->tablePrefix, $this->databaseConnection->real_escape_string($this->checkedCategoryOrTag["PrimaryKey"]));
		
		if(F_MYSQLSTOREQUERIES)
		{
			array_push($this->debugQueries, $query);
		}
			
		$this->queries++;
		
		$tempArray = array();
		
		
		if($this->haveCacher && $this->cacher->checkExists(sprintf("%s%d", $query, 1)) && $this->cacher->checkExists($query))
		{
			$queryString = $this->cacher->getCachedData();
			$this->haveNextPage = $this->cacher->getCachedData();
		}
		else if($result = $this->databaseConnection->query($query))
		{
			while($row = $result->fetch_assoc())
			{
				array_push($tempArray, $row["PostID"]);
			}
			
			$result->close();
			
			$queryString = implode(", ", $tempArray);
			
			if($this->haveCacher)
			{
				//echo $query;
				$this->cacher->writeCachedFile($query, $queryString);
			}
		}
		
		
		if($queryString != null)
		{
			if(!$draft)
			{
				$query = sprintf("SELECT * FROM %sposts WHERE Draft='0' AND PrimaryKey IN (%s) ORDER BY Date DESC LIMIT %s OFFSET %s", $this->tablePrefix, $this->databaseConnection->real_escape_string($queryString), $this->databaseConnection->real_escape_string($limit), $this->databaseConnection->real_escape_string($offset));
			}
			else
			{
				$query = sprintf("SELECT * FROM %sposts WHERE PrimaryKey IN (%s)", $this->tablePrefix, $this->databaseConnection->real_escape_string($queryString));
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
			else if($result = $this->databaseConnection->query($query))
			{
				$tempAuthor = array();
				while($row = $result->fetch_assoc())
				{
					if(!isset($tempAuthor[$row["Author"]]))
					{
						$tempAuthor[$row["Author"]] = $row["Author"];
					}
					
					array_push($return, $row);
				}
				$result->close();
				
				$return = $this->generateAuthors($return, $tempAuthor);
				
				$returnCout = count($return);
				if($returnCout == $limit)
				{
					$this->haveNextPage = true;
					array_pop($return);
				}
				unset($tempAuthor);
				
				if($this->haveCacher)
				{
					$this->cacher->writeCachedFile($query, $return);
					$this->cacher->writeCachedFile(sprintf("%s%d", $query, 1), $this->haveNextPage);
				}
			}
		}
		return $return;
	}
	
	public function getCategoryOrTag($ID, $type)
	{
		
	}
	
	/**
	 * checkCategoryOrTagName function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @param mixed $type
	 * @return void
	 */
	public function checkCategoryOrTagName($name, $type)
	{
		$return = false;
		
		$query = sprintf("SELECT * FROM %scatstags WHERE URIName='%s' AND Type='%s'", $this->tablePrefix, $this->databaseConnection->real_escape_string($name), $this->databaseConnection->real_escape_string($type));
		
		if(F_MYSQLSTOREQUERIES)
		{
			array_push($this->debugQueries, $query);
		}
			
		$this->queries++;
		
		if($this->haveCacher && $this->cacher->checkExists($query))
		{
			$temp = $this->cacher->getCachedData();
			
			if($temp != null)
			{
				$return = true;
				$this->checkedCategoryOrTag = $temp;
			}
			//$this->checkedCategoryOrTag = $this->cacher->getCachedData();
		}
		else if($result = $this->databaseConnection->query($query))
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
		$query = sprintf("SELECT * FROM %scatstags WHERE Type='%s'", $this->tablePrefix, $this->databaseConnection->real_escape_string($type));
		
		if($this->haveCacher && $this->cacher->checkExists($query))
		{
			$return = $this->cacher->getCachedData();
		}
		else if($result = $this->databaseConnection->query($query))
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
		return $this->haveNextPage;
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
			$query = $this->databaseConnection->prepare($formattedQuery);
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
			$query = $this->databaseConnection->prepare($formattedQuery);
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
		
		if($result = $this->databaseConnection->query($query))
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