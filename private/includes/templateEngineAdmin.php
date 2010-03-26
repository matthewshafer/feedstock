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
	protected $theData = array();
	private $haveNextPage = false;
	private $count = -1;
	
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
			$return .= $this->itsNotMeItsYou();
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
		
		switch(strtolower($this->router->pageType()))
		{
			case "":
				$return = "/index.php";
				// want to set up the page variables and stuff, yay
				break;
			case "post":
				$return = "/createPost.php";
				// set up some vars, we want to check if we have an ID in the uri and if we do we need to load that from the db and set up the vars
				if($this->router->getUriPosition(2) != null)
				{
					$this->theData = $this->db->getPostDataByID(intval($this->router->getUriPosition(2)));
				}
				break;
			case "page":
				$return = "/createPage.php";
				// set up some vars, we want to check if we have an ID in the uri and we need to load that and set up some stuff
				if($this->router->getUriPosition(2) != null)
				{
					$this->theData = $this->db->getPageDataByID(intval($this->router->getUriPosition(2)));
				}
				break;
			case "posts":
				$return = "/postList.php";
				// gonna want to set some stuff up
				//$this->theData = $this->db->getPostList();
				$this->theData = $this->getPostOrPageList("post");
				break;
			case "pages":
				$return = "/pageList.php";
				// gonna want to set some stuff up
				$this->theData = $this->getPostOrPageList("page");
				break;
		}
		
		if($return == null)
		{
			// lets make it something that doesnt exist
			$return = "/404.php";
		}
		
		return $return;
	}
	
	private function getPostOrPageList($type)
	{
		$offset = intval($this->router->getUriPosition(2));
		$tmpArr = array();
		// the one thing is if you do like /posts/omg it just sends you to the begining
		if($offset < 2 or $offset == null)
		{
			$offset = 0;
		}
		else
		{
			$offset = $offset - 1;
		}
		// sets up the offset 
		// need to run some tests to see if we need this. Unfortunately I don't have internet right now so I cant check this out
		$offset = $offset * 10;
		// we are doing 1 more than what we want for the limit so we can find out if there would be another page
		$limit = 11;
		
		
		if($type == "post")
		{
			$tmpArr = $this->db->getPostList($limit, $offset);
		}
		else
		{
			$tmpArr = $this->db->getPageList($limit, $offset);
		}
		
		if(count($tmpArr) == $limit)
		{
			$this->haveNextPage = true;
		}
		
		if(!isset($tmpArr[0]["PrimaryKey"]))
		{
			array_pop($tmpArr);
		}
		
		return $tmpArr;
	}
	
	public function getTheData()
	{
		return $this->theData;
	}
	
	public function postTitleID()
	{
		return "title";
	}
	
	public function postBodyID()
	{
		return "body";
	}
	
	public function postCategoriesID()
	{
		return "cat1, cat2, cat3";
	}
	
	public function postTagsID()
	{
		return "tag1, tag2, tag3";
	}
	
	public function postID()
	{
		return -1;
	}
	
	public function pageTitleID()
	{
		return "title";
	}
	
	public function pageBodyID()
	{
		return "body";
	}
	
	public function pageID()
	{
		return -1;
	}
}
?>