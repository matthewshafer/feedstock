<?php

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
		//echo V_THEME;
		//echo $address;
		$this->address = $address;
		$this->password = $password;
		$this->username = $username;
		$this->database = $database;
		$this->tableprefix = $tableprefix;
		
		require_once("includes/router.php");
		
		$this->router = new router(V_HTACCESS);
		
		if($this->router->requestMethod() == "GET")
		{
		
			if(V_CACHE)
			{
				// just a debug line
				//echo "cacher is true!";
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
					$themeData = $this->heavyLift();
					echo $themeData;
					
					if($this->router->pageType() != "file")
					{
						$this->cacher->writeCachedFile($themeData);
					}
				}
			
			}
			else
			{
				echo $this->heavyLift();
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
		//$this->db->getPosts(0);
				
		if($this->router->pageType() == "feed")
		{
			require_once("includes/feed.php");
			$feed = new feed($this->db, $this->router);
			$data = $feed->render();
		}
		else if($this->router->pageType() == "file")
		{
			//echo "file will be here sometime soon<br>";
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
		return $data;
	}
	
	 /**
	  * talk function.
	  * 
	  * @access public
	  * @return void
	  */
	 public function talk()
	{
		echo $this->address;
		echo $this->username;
		echo $this->password;
		echo $this->database;
		echo $this->tableprefix;
	}
	
	/**
	 * test function.
	 * 
	 * @access public
	 * @return void
	 */
	public function test()
	{
		//include("test.php");
	}
	
	//function visions()

}

?>