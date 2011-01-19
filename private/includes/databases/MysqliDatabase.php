<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief using mysqli which is a part of php5.  Uses things like multi_query.
 * 
 */
class MysqliDatabase
{
	private $username = null;
	private $password = null;
	private $serverAddress = null;
	private $databaseName = null;
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
	public function __construct($username, $password, $serverAddress, $databaseName, $tablePrefix, $cacher = null, $lazy = true)
	{
		$this->username = $username;
		$this->password = $password;
		$this->serverAddress = $serverAddress;
		$this->databaseName = $databaseName;
		$this->tablePrefix = $tablePrefix;
		
		if(!$lazy)
		{
			$this->lazyConnect();
		}
		
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
	}
	
	private function lazyConnect()
	{
		$return = true;
		
		if(!is_resource($this->databaseConnection))
		{
			if(!$this->databaseConnection = new mysqli($this->serverAddress, $this->username, $this->password, $this->databaseName))
			{
				trigger_error(sprintf("Cannot connect to server: %s", mysql_error()), E_USER_ERROR);
				$return = false;
			}
		}
		
		return $return;
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
		if(is_resource($this->databaseConnection))
		{
			$this->databaseConnection->close();
		}
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
			$stockQuery = "SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author WHERE Draft='0' ORDER BY Date DESC LIMIT %s OFFSET %s";
			$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $limit, $offset);
			
			
			if($this->debugSwitch)
			{
				array_push($this->debugQueries, $nonEscapedQuery);
			}
			
			$this->queries++;
		}
		else
		{
			//$query = sprintf("SELECT * FROM %sposts order by Date DESC LIMIT %s OFFSET %s", $this->tablePrefix, $this->databaseConnection->real_escape_string($limit), $this->databaseConnection->real_escape_string($offset));
			
			$stockQuery = "SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author  ORDER BY Date DESC LIMIT %s OFFSET %s";
			$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $limit, $offset);
			$this->queries++;
		}
		
		
		if($this->haveCacher && $this->cacher->checkExists(sprintf("%s%d", $nonEscapedQuery, 1)) && $this->cacher->checkExists($nonEscapedQuery))
		{
			$return = $this->cacher->getCachedData();
			$this->haveNextPage = $this->cacher->getCachedData();
		}
		else if($this->lazyConnect())
		{
		
			$escapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $this->databaseConnection->real_escape_string($limit), $this->databaseConnection->real_escape_string($offset));
			

			
			if($result = $this->databaseConnection->query($escapedQuery))
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
					$this->cacher->writeCachedFile(sprintf("%s%d", $nonEscapedQuery, 1), $this->haveNextPage);
					$this->cacher->writeCachedFile($nonEscapedQuery, $return);
				}
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
			$stockQuery = "SELECT * FROM %spages WHERE URI='/%s' AND Draft='0'";
			$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $uri);
			
			if($this->debugSwitch)
			{
				array_push($this->debugQueries, $nonEscapedQuery);
			}
			
			$this->queries++;
		}
		else
		{
			$stockQuery = "SELECT * FROM %spages WHERE URI='/%s'";
			$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $uri);
		}
		if($this->haveCacher && $this->cacher->checkExists($nonEscapedQuery))
		{
			$return = $this->cacher->getCachedData();
		}
		else if($this->lazyConnect())
		{
			$escapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->databaseConnection->real_escape_string($uri));
			
			if($result = $this->databaseConnection->query($escapedQuery))
			{
				$tmp = $result->fetch_assoc();
				if(!empty($tmp))
				{
					array_push($return, $tmp);
				}
			
				if($this->haveCacher)
				{
					$this->cacher->writeCachedFile($nonEscapedQuery, $return);
				}
				$result->close();
			}
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
			$stockQuery = "SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author WHERE URI='/%s' AND Draft='0'";
			$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $uri);
			
			if($this->debugSwitch)
			{
				array_push($this->debugQueries, $nonEscapedQuery);
			}
			$this->queries++;
		}
		else
		{
			$stockQuery = "SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author WHERE URI='/%s'";
			$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $uri);
		}
		
		if($this->haveCacher && $this->cacher->checkExists($nonEscapedQuery))
		{
			$tempArray = $this->cacher->getCachedData();
			
		}
		else if($this->lazyConnect())
		{
			$escapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $this->databaseConnection->real_escape_string($uri));
			
			if($result = $this->databaseConnection->query($escapedQuery))
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
					$this->cacher->writeCachedFile($nonEscapedQuery, $tempArray);
				}
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
	 	$arrayWithCatsAndTags = array();
	 	static $categoryArray = array();
	 	static $tagArray = array();
	 	static $hasRunQuery = false;
	 	$catsTagsArrayLength = 0;
	 	$return = array();
	 	
	 	
	 	if(!$hasRunQuery)
	 	{
	 		$queryString = implode(", ", $idArray);
	 		
	 		$stockQuery = "SELECT Name, URIName, Type, SubCat, PostID FROM %sposts_tax LEFT JOIN %scatstags ON PrimaryKey = CatTagID WHERE PostID IN (%s) AND PrimaryKey IS NOT NULL ORDER BY PostID DESC";
	 		$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $queryString);
	 		
	 		
	 		
	 		if($this->debugSwitch)
			{
				array_push($this->debugQueries, $nonEscapedQuery);
			}
			$this->queries++;
			
	 		
	 		if($this->haveCacher && $this->cacher->checkExists($nonEscapedQuery))
	 		{
	 			$arrayWithCatsAndTags = $this->cacher->getCachedData();
	 		}
	 		else if($this->lazyConnect())
	 		{
	 			$escapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $this->databaseConnection->real_escape_string($queryString));
	 			
	 			if($result = $this->databaseConnection->query($escapedQuery))
	 			{
	 			
	 				while($row = $result->fetch_assoc())
	 				{
	 					array_push($arrayWithCatsAndTags, $row);
	 				}
	 			
	 				if($this->haveCacher)
	 				{
	 					$this->cacher->writeCachedFile($nonEscapedQuery, $arrayWithCatsAndTags);
	 				}
	 			}
	 		}
	 		
	 		$catsTagsArrayLength = count($arrayWithCatsAndTags);
	 		
	 		for($i = 0; $i < $catsTagsArrayLength; $i++)
	 		{
	 			if($arrayWithCatsAndTags[$i]["Type"] == 1)
	 			{
	 				// tag
	 				
	 				if(!isset($tagArray[$arrayWithCatsAndTags[$i]["PostID"]]))
	 				{
	 					$tagArray[$arrayWithCatsAndTags[$i]["PostID"]] = array();
	 				}
	 				
	 				array_push($tagArray[$arrayWithCatsAndTags[$i]["PostID"]], $arrayWithCatsAndTags[$i]);
	 			}
	 			else
	 			{
	 				// category
	 				if(!isset($categoryArray[$arrayWithCatsAndTags[$i]["PostID"]]))
	 				{
	 					$categoryArray[$arrayWithCatsAndTags[$i]["PostID"]] = array();
	 				}
	 				
	 				array_push($categoryArray[$arrayWithCatsAndTags[$i]["PostID"]], $arrayWithCatsAndTags[$i]);
	 			}
	 		}
	 		
	 		$hasRunQuery = true;
	 		
	 	}
	 	
	 	if($type == 1)
	 	{
	 		$return = $tagArray;
	 	}
	 	else
	 	{
	 		$return = $categoryArray;
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
		$stockQuery = "SELECT PostID FROM %sposts_tax WHERE CatTagID='%s'";
		$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->checkedCategoryOrTag["PrimaryKey"]);
		
		if($this->debugSwitch)
		{
			array_push($this->debugQueries, $nonEscapedQuery);
		}
			
		$this->queries++;
		
		$tempArray = array();
		
		
		if($this->haveCacher && $this->cacher->checkExists($nonEscapedQuery))
		{
			$queryString = $this->cacher->getCachedData();
		}
		else if($this->lazyConnect())
		{
			$escapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->databaseConnection->real_escape_string($this->checkedCategoryOrTag["PrimaryKey"]));
			
			if($result = $this->databaseConnection->query($escapedQuery))
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
					$this->cacher->writeCachedFile($nonEscapedQuery, $queryString);
				}
			}
		}
		
		
		if($queryString != null)
		{
			if(!$draft)
			{
				$stockQuery = "SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author WHERE PrimaryKey IN (%s) AND Draft='0' ORDER BY Date DESC LIMIT %s OFFSET %s";
				$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $queryString, $limit, $offset);
			}
			else
			{
				$stockQuery = "SELECT PrimaryKey, Title, NiceTitle, URI, PostData, Category, Tags, Date, themeFile, Draft, displayName AS Author FROM %sposts LEFT JOIN %susers ON id = Author WHERE PrimaryKey IN (%s) ORDER BY Date DESC LIMIT %s OFFSET %s";
				$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $queryString, $limit, $offset);
			}
			
			if($this->debugSwitch)
			{
				array_push($this->debugQueries, $nonEscapedQuery);
			}
	
			$this->queries++;
			
			if($this->haveCacher && $this->cacher->checkExists($nonEscapedQuery) && $this->cacher->checkExists(sprintf("%s%d", $nonEscapedQuery, 1)))
			{
				$return = $this->cacher->getCachedData();
				$this->haveNextPage = $this->cacher->getCachedData();
				//echo "\nhere\n";
			}
			else if($this->lazyConnect())
			{
				$escapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->tablePrefix, $this->databaseConnection->real_escape_string($queryString), $this->databaseConnection->real_escape_string($limit), $this->databaseConnection->real_escape_string($offset));
				
				if($result = $this->databaseConnection->query($escapedQuery))
				{
					while($row = $result->fetch_assoc())
					{
						if($row["Author"] == null)
						{
							$row["Author"] = "Unknown";
						}
					
						array_push($return, $row);
					}
					$result->close();
					
					
					$returnCout = count($return);
					if($returnCout == $limit)
					{
						$this->haveNextPage = true;
						array_pop($return);
					}
					
					if($this->haveCacher)
					{
						$this->cacher->writeCachedFile($nonEscapedQuery, $return);
						$this->cacher->writeCachedFile(sprintf("%s%d", $nonEscapedQuery, 1), $this->haveNextPage);
					}
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
		$stockQuery = "SELECT * FROM %scatstags WHERE URIName='%s' AND Type='%s'";
		$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $name, $type);
		
		if($this->debugSwitch)
		{
			array_push($this->debugQueries, $nonEscapedQuery);
		}
			
		$this->queries++;
		
		if($this->haveCacher && $this->cacher->checkExists($nonEscapedQuery))
		{
			$temp = $this->cacher->getCachedData();
			
			if($temp != null)
			{
				$return = true;
				$this->checkedCategoryOrTag = $temp;
			}
			//$this->checkedCategoryOrTag = $this->cacher->getCachedData();
		}
		else if($this->lazyConnect())
		{
			$escapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->databaseConnection->real_escape_string($name), $this->databaseConnection->real_escape_string($type));
			
			if($result = $this->databaseConnection->query($escapedQuery))
			{
				if($row = $result->fetch_assoc())
				{
					$return = true;
				
					$this->checkedCategoryOrTag = $row;
				}
				
				$result->close();
				
				if($this->haveCacher)
				{
					$this->cacher->writeCachedFile($nonEscapedQuery, $this->checkedCategoryOrTag);
				}
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
		$stockQuery = "SELECT * FROM %scatstags WHERE Type='%s'";
		$nonEscapedQuery = sprintf($stockQuery, $this->tablePrefix, $type);
		
		if($this->haveCacher && $this->cacher->checkExists($nonEscapedQuery))
		{
			$return = $this->cacher->getCachedData();
		}
		else if($this->lazyConnect())
		{
			$escapedQuery = sprintf($stockQuery, $this->tablePrefix, $this->databaseConnection->real_escape_string($type));
			
			if($result = $this->databaseConnection->query($escapedQuery))
			{
				while($row = $result->fetch_assoc())
				{
					array_push($return, $row);
				}
				
				$result->close();
				
				if($this->haveCacher)
				{
					$this->cacher->writeCachedFile($nonEscapedQuery, $return);
				}
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
		else if($this->lazyConnect())
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
		else if($this->lazyConnect())
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