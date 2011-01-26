<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Makes everything we need to generate the front facing pages. You can think of it as the brain's of the operation.
 * 
 */
class Feedstock
{
	private $templateEngine = null;
	private $outputHelper = null;
	private $templateLoader = null;
	private $cacherCreator = null;
	
	// config data array
	private $config = array();
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		require("../config.php");
		// We need to set these because they are used in another function in this class. If we didn't they would be in the wrong scope (that wouldn't be a good thing)
		
		require_once("includes/Router.php");
		$this->router = new Router($this->config['htaccess'], $this->config['siteUrlBase']);
		
		require_once("includes/OutputHelper.php");
		$this->outputHelper = new OutputHelper();
		
		
		try
		{
			$this->handleRequest();
		}
		catch(Exception $e)
		{
			$this->outputHelper->stopStoreGetBuffer();
			echo $e->getMessage();
		}
	}
	
	private function handleRequest()
	{
		if($this->router->requestMethod() == "GET")
		{
			if($this->maintenanceMode($this->config['enableMaintenance'], $this->config['maintenancePassthru']))
			{
				require_once("includes/Maintenance.php");
				$maintenance = new Maintenance(sprintf("%s/private/themes/%s/maintenance.php", $this->config['baseLocation'], $this->config['themeName']), $this->outputHelper);
				$maintenance->render();
			}
			else
			{
				require_once("includes/CacherCreator.php");
				$this->cacherCreator = new CacherCreator($this->config['cacheName'], $this->config['cachePrefix'], $this->config['cacheExpireTime'], $this->config['baseLocation']);
				
			
				if($this->config['cacheEnable'] && $this->config['cacheType'] === "static" && $this->cacherCreator->createCacher())
				{
					// Should create the cacher first so that we can check if a file exists before we even create a database
					// for example if the database goes down we can still serve up pages, until they "expire" which would give us
					// a little bit of time to get the DB back up and running
					
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
					
						if($themeData != null)
						{
							$this->cacher->writeCachedFile($this->router->fullUri(), $themeData);
						}
						//echo $themeData;
					}
				}
				else
				{
					$this->heavyLift();
					
					if($this->config['databaseDebug'])
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
		$data = null;
		

		// ok so really we only need the database when it comes to keeping track of files, which we currently don't do. Decisions decisions.
		if($this->router->pageType() == "file")
		{
			require_once("includes/FileServe.php");
			$fileServe = new FileServe($this->database, $this->router, $this->config['baseLocation'], $this->config['enableFileDownload']);
			$fileServe->setDownloadSpeed($this->fileDownloadSpeed);
			$data = $fileServe->render();
		}
		else
		{
			require_once("includes/SiteUrlGenerator.php");
			$siteUrlGenerator = new SiteUrlGenerator($this->config['siteUrl'], $this->config['siteUrlBase'], $this->config['htaccess']);
			require_once("includes/TemplateEngine.php");
			
			$this->templateEngine = new TemplateEngine($this->database, 
														$this->router, 
														$this->config['siteTitle'], 
														$this->config['siteDescription'], 
														$this->config['themeName'], 
														$siteUrlGenerator->generateSiteUrl(), 
														$this->config['postFormat'], 
														$this->config['postsPerPage'], 
														$this->config['baseLocation']);
																
			$this->templateEngine->setFeedAuthorInfo($this->config['feedAuthor'], $this->config['feedAuthorEmail']);
			$this->templateEngine->setPubSubHubBub($this->config['feedPubSubHubBub'], $this->config['feedPubSubHubBubSubscribe']);
			
			require_once("includes/TemplateLoader.php");
			$this->templateLoader = new TemplateLoader($this->templateEngine, $this->outputHelper);
			$data = $this->templateLoader->render();
		}
		
		$this->database->closeConnection();

		
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
		require_once("includes/databases/" . $this->config['databaseType'] . "Database.php");
		$return = null;
		$cacher = null;
		$type = $this->config['databaseType'] . "Database";
		
		if($this->config['cacheEnable'] && $this->config['cacheType'] === "dynamic" && $this->cacherCreator->createCacher())
		{
			$cacher = $this->cacheCreator->getCacher();
		}
		
		
		$return = new $type($this->config['databaseUsername'], $this->config['databasePassword'], $this->config['databaseAddress'], $this->config['databaseName'], $this->config['databaseTablePrefix'], $cacher);
		
		// add some checking if the database name is set up wrong
		if($this->config['databaseDebug'])
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