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
	private $cacherCreator = null;
	private $databaseDebug = false;
	private $enableFileDownload = false;
	private $fileDownloadSpeed = 0;
	private $maintenanceAddresses = null;
	private $cacheEnable = false;
	private $feedAuthor = "";
	private $feedAuthorEmail = "";
	private $feedPubSubHubBub = "";
	private $feedPubSubHubBubSubscribe = "";
	private $cacheName = "";
	private $cachePrefix = "";
	private $cacheType = "";
	private $cacheExpireTime = 0;
	private $htaccess = false;
	private $siteTitle = "";
	private $siteDescription = "";
	private $themeName = "";
	private $siteUrl = "";
	private $siteUrlBase = "";
	private $postFormat = "";
	private $postsPerPage = 0;
	private $databaseType = "";
	private $baseLocation = "";
	
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
		$this->databaseDebug = $databaseDebug;
		$this->enableFileDownload = $fileDownload;
		$this->fileDownloadSpeed = $fileDownloadSpeed;
		$this->maintenanceAddresses = $maintenancePassthrough;
		$this->cacheEnable = $cacheEnable;
		$this->feedAuthor = $feedAuthor;
		$this->feedAuthorEmail = $feedAuthorEmail;
		$this->feedPubSubHubBub = $feedPubSubHubBub;
		$this->feedPubSubHubBubPublishUrl = $feedPubSubHubBubPublishUrl;
		$this->feedPubSubHubBubSubscribe = $feedPubSubHubBubSubscribe;
		$this->cacheName = $cacheName;
		$this->cachePrefix = $cachePrefix;
		$this->cacheType = $cacheType;
		$this->cacheExpireTime = $cacheExpireTime;
		$this->htaccess = $htaccess;
		$this->siteTitle = $siteTitle;
		$this->siteDescription = $siteDescription;
		$this->themeName = $themeName;
		$this->postFormat = $postFormat;
		$this->postsPerPage = $postsPerPage;
		$this->siteUrl = $siteUrl;
		$this->siteUrlBase = $siteUrlBase;
		$this->databaseType = $databaseType;
		$this->baseLocation = $baseLocation;
		
		require_once("includes/Router.php");
		$this->router = new Router($this->htaccess, $this->siteUrlBase);
		
		require_once("includes/OutputHelper.php");
		$this->outputHelper = new OutputHelper();
		
		$this->handleRequest($enableMaintenance);
	}
	
	private function handleRequest($enableMaintenance)
	{
		if($this->router->requestMethod() == "GET")
		{
			if($this->maintenanceMode($enableMaintenance, $this->maintenanceAddresses))
			{
				require_once("includes/Maintenance.php");
				$maintenance = new Maintenance(sprintf("%s/private/themes/%s/maintenance.php", $this->baseLocation, $this->themeName), $this->outputHelper);
				$maintenance->render();
			}
			else
			{
				require_once("includes/CacherCreator.php");
				$this->cacherCreator = new CacherCreator($this->cacheName, $this->cachePrefix, $this->cacheExpireTime, $this->baseLocation);
				
			
				if($this->cacheEnable && $this->cacheType == "static" && $this->cacherCreator->createCacher())
				{
					// Should create the cacher first so that we can check if a file exists before we even create a database
					// for example if the database goes down we can still serve up pages, until they "expire" which would give us
					// a little bit of time to get the DB back up and running
					//require_once("includes/" . F_CACHENAME . ".php");
					//$this->cacher = new cache($this->router->fullURI());
					$this->cacher = $this->cacherCreator->getCacher();
				
					if($this->cacher->checkExists($this->router->fullUri()))
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
							$this->cacher->writeCachedFile($this->router->fullUri(), $themeData);
						}
						//echo $themeData;
					}
				}
				else
				{
					$this->heavyLift();
					
					if($this->databaseDebug)
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
		$this->database = $this->databaseMaker();
		
		if($this->database->haveConnectionError() == null)
		{
			
			// ok so really we only need the database when it comes to keeping track of files, which we currently don't do. Decisions decisions.
			if($this->router->pageType() == "file")
			{
				require_once("includes/FileServe.php");
				$fileServe = new FileServe($this->database, $this->router, $this->baseLocation, $this->enableFileDownload);
				$fileServe->setDownloadSpeed($this->fileDownloadSpeed);
				$data = $fileServe->render();
				
				if($data != null)
				{
					echo $data;
				}
			}
			else
			{
				require_once("includes/SiteUrlGenerator.php");
				$siteUrlGenerator = new SiteUrlGenerator($this->siteUrl, $this->siteUrlBase, $this->htaccess);
				require_once("includes/TemplateEngine.php");
				try
				{
					$this->templateEngine = new TemplateEngine($this->database, 
																$this->router, 
																$this->siteTitle, 
																$this->siteDescription, 
																$this->themeName, 
																$siteUrlGenerator->generateSiteUrl(), 
																$this->postFormat, 
																$this->postsPerPage, 
																$this->baseLocation);
																
					$this->templateEngine->setFeedAuthorInfo($this->feedAuthor, $this->feedAuthorEmail);
					$this->templateEngine->setPubSubHubBub($this->feedPubSubHubBub, $this->feedPubSubHubBubSubscribe);
					
					require_once("includes/TemplateLoader.php");
					$this->templateLoader = new TemplateLoader($this->templateEngine, $this->outputHelper);
					$data = $this->templateLoader->render();
				}
				catch(Exception $e)
				{
					echo $e;
				}
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
		require_once("includes/interfaces/GenericDatabase.php");
		require_once("includes/databases/" . $this->databaseType . "Database.php");
		$return = null;
		
		// not going to need this line once I finish implementing the interfaces
		switch($this->databaseType)
		{
			case "Mysqli":
				if($this->cacheEnable && $this->cacheType == "dynamic" && $this->cacherCreator->createCacher())
				{
					$return = new MysqliDatabase($this->username, $this->password, $this->address, $this->databaseName, $this->tablePrefix, $this->cacherCreator->getCacher());
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
		
		// add some checking if the database name is set up wrong
		if($this->databaseDebug)
		{
			$return->enableDebug();
		}
		
		return $return;
	}
	
	/**
	 * maintenanceMode function.
	 * 
	 * @brief Allows us to check if theres maintenance mode enabled and if the current person accessing the site can bypass maintenance mode
	 * @access private
	 * @param boolean $enabled
	 * @param mixed $allowThese
	 * @return True if we are in maintenance mode, false if not or the user can bypass it;
	 */
	private function maintenanceMode($enabled, $allowThese)
	{
		$return = false;
		
		if($enabled)
		{
			require_once("includes/IpChecker.php");
			
			$ipChecker = new IpChecker();
			
			if(!$ipChecker->checkIP($allowThese))
			{
				$return = true;
			}
		}
		
		return $return;
	}
}

?>