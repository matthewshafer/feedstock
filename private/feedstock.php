<?php
/**
* @file
* @author Matthew Shafer <matt@niftystopwatch.com>
* @brief Makes everything we need to generate the front facing pages. You can think of it as the brain's of the operation.
* 
*/
class feedstock
{
	private $username = null;
	private $password = null;
	private $address = null;
	private $database = null;
	private $tableprefix = null;
	private $cacher = null;
	private $templateEngine = null;
	private $templateLoader = null;
	private $router = null;
	private $db = null;
	
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
		$this->database = $database;
		$this->tableprefix = $tableprefix;
		
		require_once("includes/router.php");
		
		$this->router = new router(V_HTACCESS);
		
		if($this->router->requestMethod() == "GET")
		{
		
			if(V_CACHE and is_writable(V_BASELOC . "/private/cache"))
			{
				// Should create the cacher first so that we can check if a file exists before we even create a database
				// for example if the database goes down we can still serve up pages, until they "expire" which would give us
				// a little bit of time to get the DB back up and running
				require_once("includes/cache.php");
				$this->cacher = new cache($this->router->fullURI());
			
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
					echo $this->cacher->getCachedData();
				}
			
			}
			else
			{
				echo $this->heavyLift();
				//echo "<br><br>Queries: " . $this->db->queries;
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
		require_once("includes/" . V_DATABASE . ".php");
		$this->db = new database($this->username, $this->password, $this->address, $this->database, $this->tableprefix);
		
		if($this->db->haveConnError() == null)
		{		
		if($this->router->pageType() == "feed")
		{
			require_once("includes/feed.php");
			$feed = new feed($this->db, $this->router);
			$data = $feed->render();
		}
		else if($this->router->pageType() == "file")
		{
			require_once("includes/fileServe.php");
			$fileServe = new fileServe($this->db, $this->router);
			$data = $fileServe->render();
		}
		else
		{
			require_once("includes/templateEngine.php");
			$this->templateEngine = new templateEngine($this->db, $this->router);
			require_once("includes/templateLoader.php");
			$this->templateLoader = new templateLoader($this->templateEngine);
			$data = $this->templateLoader->render();
		}
		}
		else
		$data = $this->db->haveConnError();
		return $data;
	}
}

?>