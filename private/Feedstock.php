<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Makes everything we need to generate the front facing pages. You can think of it as the brain's of the operation.
 * 
 */
class Feedstock
{
	private $username = null;
	private $password = null;
	private $address = null;
	private $databaseName = null;
	private $tablePrefix = null;
	private $cacher = null;
	private $templateEngine = null;
	private $outputHelper = null;
	private $templateLoader = null;
	private $router = null;
	private $database = null;
	private $cacheHandler = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		require_once("../config.php");
		// We need to set these because they are used in another function in this class. If we didn't they would be in the wrong scope (that wouldn't be a good thing)
		$this->address = $address;
		$this->password = $password;
		$this->username = $username;
		$this->databaseName = $database;
		$this->tablePrefix = $tableprefix;
		
		require_once("includes/Router.php");
		$this->router = new Router(V_HTACCESS);
		
		require_once("includes/OutputHelper.php");
		$this->outputHelper = new OutputHelper();
		
		$this->handleRequest();
	}
	
	private function handleRequest()
	{
		if($this->router->requestMethod() == "GET")
		{
			if($this->maintenanceMode())
			{
				require_once("includes/Maintenance.php");
				$maintenance = new Maintenance(sprintf("%s/private/themes/%s/maintenance.php", V_BASELOC, V_THEME), $this->outputHelper);
				$maintenance->render();
			}
			else
			{
				require_once("includes/CacheHandler.php");
				$this->cacheHandler = new CacheHandler($this->router);
				
			
				if(V_CACHE && $this->cacheHandler->cacheType() == "static" && $this->cacheHandler->cacheWriteableLoc())
				{
					// Should create the cacher first so that we can check if a file exists before we even create a database
					// for example if the database goes down we can still serve up pages, until they "expire" which would give us
					// a little bit of time to get the DB back up and running
					//require_once("includes/" . F_CACHENAME . ".php");
					//$this->cacher = new cache($this->router->fullURI());
					$this->cacher = $this->cacheHandler->cacheMaker();
				
					if($this->cacher->checkExists())
					{
						echo $this->cacher->getCachedData();
					}
					else
					{
						// need to figure out how to grab this yet if there is an error I need to keep using cached data.
						$themeData = $this->heavyLift();
						//echo $themeData;
					
						if($this->router->pageType() != "file")
						{
							$this->cacher->writeCachedFile($themeData);
						}
						//echo $themeData;
					}
				}
				else
				{
					//echo $this->heavyLift();
					$this->heavyLift();
					//echo "<br><br>Queries: " . $this->db->queries;
					
					if(F_MYSQLSTOREQUERIES)
					{
						echo "<br><br><br>";
					//print_r($this->db->debugQueries);
						foreach($this->database->debugQueries as $key)
						{
							echo $key . "<br>";
						}
						
						//echo '<br><br>' . $this->db->queryError();
					}
				}
			}
		}
	}
	
	/**
	 * heavyLift function.
	 * 
	 * @access private
	 * @return html data
	 */
	private function heavyLift()
	{
		//require_once("includes/" . V_DATABASE . ".php");
		//$this->db = new database($this->username, $this->password, $this->address, $this->database, $this->tableprefix);
		$this->database = $this->databaseMaker();
		
		if($this->database->haveConnectionError() == null)
		{		
			//if($this->router->pageType() == "feed")
			//{
				//require_once("includes/feed.php");
				//$feed = new feed($this->db, $this->router);
				//$data = $feed->render();
			//}
			if($this->router->pageType() == "file")
			{
				require_once("includes/FileServe.php");
				$fileServe = new FileServe($this->database, $this->router);
				$data = $fileServe->render();
				
				if($data != null)
				{
					echo $data;
				}
			}
			else
			{
				require_once("includes/TemplateEngine.php");
				$this->templateEngine = new TemplateEngine($this->database, $this->router);
				require_once("includes/TemplateLoader.php");
				$this->templateLoader = new TemplateLoader($this->templateEngine, $this->outputHelper);
				$data = $this->templateLoader->render();
			}
			
			$this->database->closeConnection();
		}
		else
		{
			$data = $this->database->haveConnError();
		}
		return $data;
	}
	
	/**
	 * databaseMaker function.
	 * 
	 * @brief Makes it easy to add new databases to Feedstock
	 * @access private
	 * @return Database Object or null;
	 */
	private function databaseMaker()
	{
		require_once("includes/" . V_DATABASE . ".php");
		$return = null;
		
		switch(V_DATABASE)
		{
			case "Mysqli":
				if(V_CACHE && $this->cacheHandler->cacheType() == "dynamic")
				{
					$return = new MysqliDatabase($this->username, $this->password, $this->address, $this->databaseName, $this->tablePrefix, $this->cacheHandler->cacheMaker());
				}
				else
				{
					$return = new MysqliDatabase($this->username, $this->password, $this->address, $this->databaseName, $this->tablePrefix);
				}
			break;
			case "Mysql":
				$return = new MysqlDatabase($this->username, $this->password, $this->address, $this->databaseName, $this->tablePrefix);
			break;
		}
		
		return $return;
	}
	
	/**
	 * maintenanceMode function.
	 * 
	 * @brief Allows us to check if theres maintenance mode enabled and if the current person accessing the site can bypass maintenance mode
	 * @access private
	 * @return True if we are in maintenance mode, false if not or the user can bypass it;
	 */
	private function maintenanceMode()
	{
		$return = false;
		
		if(F_MAINTENANCE)
		{
			require_once("includes/IpChecker.php");
			
			$ipChecker = new IpChecker();
			
			if(!$ipChecker->checkIP(F_MAINTENANCEPASS))
			{
				$return = true;
			}
		}
		
		return $return;
	}
}

?>