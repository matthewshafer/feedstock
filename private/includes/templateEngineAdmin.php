<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Does all the heavy lifting for admin page themes
 * 
 */
class templateEngineAdmin
{
	protected $db;
	protected $router;
	protected $isLoggedIn = false;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $database
	 * @param mixed $router
	 * @return void
	 */
	public function __construct($database, $router)
	{
		$this->db = $database;
		$this->router = $router;
	}
	
	public function loggedIn($areWe)
	{
		$this->isLoggedIn = $areWe;
	}
	
	/**
	 * getThemeLoc function.
	 * 
	 * @access public
	 * @param bool $loggedIn. (default: false)
	 * @return void
	 */
	public function getThemeLoc()
	{
		return $this->request();
	}
	
	/**
	 * themeFileIsValid function.
	 * 
	 * @brief Checks admin theme files, only because I allow custom themes to be created otherwise I could do without this
	 * @access private
	 * @param mixed $file
	 * @return void
	 */
	private function themeFileIsValid($file)
	{
		// need to fix the part that says stock because what if you were using a different theme.
		$loc = V_BASELOC . "/private/themesAdmin/" . "stock";
		$return = null;
		
		if(file_exists($loc . "/" . $file) && is_readable($loc . "/" . $file))
		{
			$return = true;
		}
		else
		{
			$return = false;
		}
		
		return $return;
	}
	
	/**
	 * request function.
	 * 
	 * @access private
	 * @return void
	 */
	private function request()
	{
		$return = V_BASELOC . "/private/themesAdmin/" . "stock";
		
		if($this->isLoggedIn)
		{
			$return .= "/login.php";
		}
		else
		{
			$return .= "/login.php";
		}
		
		return $return;
	}
	
	private function itsNotMeItsYou()
	{
		$return = null;
		
		switch(strtolower($this->router->pageType()) == ""))
		{
			case "":
				$return = "/index.php";
				// want to set up the page variables and stuff, yay
				break;
			case "post":
				$return = "/createPost.php";
				// set up some vars, we want to check if we have an ID in the uri and if we do we need to load that from the db and set up the vars
				break;
			case "page":
				$return = "/createPage.php";
				// set up some vars, we want to check if we have an ID in the uri and we need to load that and set up some stuff
				break;
			case "posts":
				$return = "/postList.php";
				// gonna want to set some stuff up
				break;
			case "pages":
				$return = "/pagesList.php";
				// gonna want to set some stuff up
				break;
		}
		
		if($return == null)
		{
			// lets make it something that doesnt exist
			$return = "/404.php";
		}
	}
}
?>