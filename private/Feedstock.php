<?php
/**
 * Makes everything we need to generate the front facing pages. You can think of it as the brain's of the operation.
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * 
 */
class Feedstock
{
	private $templateEngine = null;
	private $outputHelper = null;
	private $templateLoader = null;
	private $cacherCreator = null;
	private $templateRouter = null;
	
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
		// this is the fun part where we break down the URI, it just stores the stuff for us to use later
		$this->router->buildRouting();
		
		require_once("includes/OutputHelper.php");
		$this->outputHelper = new OutputHelper();
		
		// attempts to break down the uri request and get the data for the user
		// if this fails then we echo a message out to the user
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
	
	
	/**
	 * handleRequest function.
	 * 
	 * First part in figuring out what to do. Checks for maintenance mode and creates the cacher's if needed to generate a page
	 * @access private
	 * @return void
	 */
	private function handleRequest()
	{
		// making sure this is a GET request.  We currently do not support any other forms of requests.
		if($this->router->requestMethod() === "GET")
		{
			// checks for maintenance mode
			if($this->maintenanceMode($this->config['enableMaintenance'], $this->config['maintenancePassthru']))
			{
				require_once("includes/Maintenance.php");
				$themeLoc = $this->config['baseLocation'] . '/private/themes/' . $this->config['themeName'] . '/maintenance.php';
				$maintenance = new Maintenance($themeLoc, $this->outputHelper);
				$maintenance->render();
			}
			else
			{
				// builds a cacherCreator object so we can create caching objects for the database
				require_once("includes/CacherCreator.php");
				$this->cacherCreator = new CacherCreator($this->config['cacheName'], $this->config['cachePrefix'], $this->config['cacheExpireTime'], $this->config['baseLocation']);
				
				// checks to see if caching is enabled and if its set to static caching we make a static page cacher.
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
					// we don't need to worry about the return value here because we are not storing it in the cacher
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
	 * Handles the creating and rendering of a page
	 * @access private
	 * @return string|null Generated html from the page
	 */
	private function heavyLift()
	{		
		$this->database = $this->databaseMaker();
		$data = null;
		$pageType = $this->router->pageType();
		
		// allows us to remove some duplicate code as the feed and the regular loading share some common objects
		require_once("includes/SiteUrlGenerator.php");
		$siteUrlGenerator = new SiteUrlGenerator($this->config['siteUrl'], $this->config['siteUrlBase'], $this->config['htaccess'], $this->router);
		
		// templateData holds an alias to the actual object we are creating here.  This allows us to 
		// create the template engine and pass it templateData and later on add data to templateData and templateEngine see that data.
		require_once("includes/TemplateData.php");
		$templateData = new TemplateData();
		
		require_once("includes/TemplateRouter.php");
		$this->templateRouter = new TemplateRouter($this->router, 
											$this->database, 
											$templateData, 
											$this->config['baseLocation'], 
											$this->config['themeName'], 
											$pageType, 
											$this->config['postsPerPage'], 
											$this->config['postFormat']);
		
		
		require_once("includes/TemplateEngine.php");
		$this->templateEngine = new TemplateEngine($this->database, 
													$this->router, 
													$this->config['siteTitle'], 
													$this->config['siteDescription'], 
													$this->config['themeName'], 
													$siteUrlGenerator, 
													$this->config['postsPerPage'], 
													$this->config['baseLocation'], 
													$templateData);
													
		// loads the feedEngine and uses objects created above									
		if($pageType === "feed")
		{
			require_once("includes/feed/FeedEngine.php");
			$feedEngine = new FeedEngine($this->config['feedAuthor'], $this->config['feedAuthorEmail'], $this->config['feedPubSubHubBub'], $this->config['feedPubSubHubBubSubscribe']);
	
			require_once("includes/feed/FeedLoader.php");
			$feedLoader = new FeedLoader($this->router, $this->outputHelper, $feedEngine, $this->templateEngine);
			$data = $feedLoader->loadFeed();
		}
		else
		{
			try
			{
				$themeLocation = $this->templateRouter->templateFile();
				$this->templateEngine->processTemplateData();
			}
			// catching an exception if it is either related to something not existing or the template enging not being able to process the template data
			// we look for a valid 404 page and if that exists that gets rendered
			// if one does not exist then we throw a new exception that gets caught around handleRequest();
			catch(Exception $e)
			{
				$tmpTheme = $this->templateRouter->valid404Page($found);
				if($found)
				{
					$themeLocation = $tmpTheme;
				}
				else
				{
					// this exception gets caught by the try/catch block around $this->handleRequest();
					throw new Exception($e->getMessage());
				}
			}
			
			
			require_once("includes/TemplateLoader.php");
			$this->templateLoader = new TemplateLoader($themeLocation, $this->templateEngine, $this->outputHelper);
			$data = $this->templateLoader->render();
		}

	
		$this->database->closeConnection();

		
		return $data;
	}
	
	/**
	 * databaseMaker function.
	 * 
	 * Makes it easy to add new databases to Feedstock
	 * @access private
	 * @return GenericDatabase|null Database object or null if one could not be created;
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
			$cacher = $this->cacherCreator->getCacher();
		}
		
		
		$return = new $type($this->config['databaseUsername'], $this->config['databasePassword'], $this->config['databaseAddress'], $this->config['databasePort'], $this->config['databaseName'], $this->config['databaseTablePrefix'], $cacher);
		
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
	 * Allows us to check if theres maintenance mode enabled and if the current person accessing the site can bypass maintenance mode
	 * @access private
	 * @param boolean $enabled
	 * @param mixed $allowThese
	 * @return boolean True if we are in maintenance mode, false if not or the user can bypass it;
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