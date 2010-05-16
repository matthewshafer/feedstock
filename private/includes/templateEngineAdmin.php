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
	protected $theCategoryData = array();
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
	
	/**
	 * itsNotMeItsYou function.
	 * 
	 * @brief Figures out which template file needs to be loaded
	 * @access private
	 * @return String with the name of the template file to load
	 */
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
				$this->theCategoryData = $this->getCategoriesList(intval($this->router->getUriPosition(2)));
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
			case "categories":
				$return = "/categories.php";
				$this->theCategoryData = $this->getCategoriesList();
				break;
			case "corral":
				$return = "/corral.php";
				$this->theData = $this->getCorrals();
				break;
			case "tags":
				break;
			case "snippets":
				$return = "/snippet.php";
				//need to get the data but i need to make a function first
				$this->theData = $this->db->getSnippetList();
				break;
			case "snippet":
				$return = "/createSnippet.php";
				// also need to get data
				if($this->router->getUriPosition(2) != null)
				{
					$this->theData = $this->db->getSnippetByID($this->router->getUriPosition(2));
				}
				break;
		}
		
		if($return == null)
		{
			// lets make it something that doesnt exist
			$return = "/404.php";
		}
		
		return $return;
	}
	
	/**
	 * getCorrals function.
	 * 
	 * @brief Grabs all the corrals currently used.
	 * @access private
	 * @return Array with corrals, empty if none
	 */
	private function getCorrals()
	{
		$return = array();
		$specificCorral = $this->router->getUriPosition(2);
		
		if($specificCorral == null)
		{
			$return = $this->db->getCorralList();
		}
		else
		{
			// going to get a specific corral
			$return = $this->db->getPagesInCorral($specificCorral);
		}
		
		return $return;
	}
	
	/**
	 * getPostOrPageList function.
	 * 
	 * @brief Get's the data needed for listing either posts or pages
	 * @access private
	 * @param mixed $type
	 * @return Array with posts or pages, null if none
	 */
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
			//$tmpArr = $this->db->getPostList($limit, $offset);
			// this one is from the normal database class.  It get's us things like author
			$tmpArr = $this->db->getPosts(99999999, $offset, true);
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
	
	/**
	 * getCategoriesList function.
	 * 
	 * @brief Get's a list of all the added categories
	 * @access private
	 * @param mixed $id. (default: null)
	 * @return Array with the current categories, null if none
	 */
	private function getCategoriesList($id = null)
	{
		$tmpArr = $this->db->listCategoriesOrTags(0);
		$tmpArr3 = array();
		
		if($id != null)
		{
			$tmpArr2 = $this->db->getSinglePostCategories($id);
			
			foreach($tmpArr as $key)
			{
				$found = false;
				
				foreach($tmpArr2 as $key2)
				{
					if($key2["CatTagID"] == $key["PrimaryKey"])
					{
						$found = true;
					}
				}
				
				$key["Checked"] = ($found == true ? 1 : 0);
				
				array_push($tmpArr3, $key);
			}
			
			//print_r($tmpArr3);
			$tmpArr = $tmpArr3;
		}
		
		return $tmpArr;
	}
	
	/**
	 * getTheData function.
	 * 
	 * @brief Returns theData array.  The data depends on what page has been loaded
	 * @access public
	 * @return Array of data
	 */
	public function getTheData()
	{
		return $this->theData;
	}
	
	/**
	 * getCategoryData function.
	 * 
	 * @brief Returns theCategoryData array, if called when it has not been generated you get null
	 * @access public
	 * @return Array with category data
	 */
	public function getCategoryData()
	{
		return $this->theCategoryData;
	}
	
	/**
	 * postTitleID function.
	 * 
	 * @brief Simply returns the post title
	 * @access public
	 * @return String with the title of the post.  If it doesn't exist then null
	 */
	public function postTitleID()
	{
		if(isset($this->theData["Title"]))
		{
			$return = $this->theData["Title"];
		}
		else
		{
			$return = null;
		}
		return $return;
	}
	
	/**
	 * postBodyID function.
	 * 
	 * @brief Simply returns the post's body
	 * @access public
	 * @return String with the body of the post
	 */
	public function postBodyID()
	{
		if(isset($this->theData["PostData"]))
		{
			$return = $this->theData["PostData"];
		}
		else
		{
			$return = null;
		}
		return $return;
	}
	
	
	
	public function postCategoriesID()
	{
		return "cat1, cat2, cat3";
	}
	
	/**
	 * postTagsID function.
	 * 
	 * @brief Builds a correctly formatted tag string so tags can be added or removed by the user
	 * @access public
	 * @return String with the Tags else blank
	 */
	public function postTagsID()
	{
		if($this->router->getUriPosition(2) != null)
		{
			$arr = $this->db->getSinglePostTags(intval($this->router->getUriPosition(2)));
			$return = implode(", ", $arr);
		}
		else
		{
			$return = "";
		}
		return $return;
	}
	
	/**
	 * postID function.
	 * 
	 * @brief Returns the ID of the post otherwise it returns -1 which stands for a new post
	 * @access public
	 * @return Integer that is the postID
	 */
	public function postID()
	{
		if(isset($this->theData["PrimaryKey"]))
		{
			$return = $this->theData["PrimaryKey"];
		}
		else
		{
			$return = -1;
		}
		return $return;
	}
	
	/**
	 * pageTitleID function.
	 * 
	 * @brief Returns the title of the page
	 * @access public
	 * @return String with the title of the page, else null
	 */
	public function pageTitleID()
	{
		if(isset($this->theData["Title"]))
		{
			$return = $this->theData["Title"];
		}
		else
		{
			$return = null;
		}
		return $return;
	}
	
	/**
	 * pageURI function.
	 * 
	 * @brief Returns the URI for the page
	 * @access public
	 * @return String with the URI, else null
	 */
	public function pageURI()
	{
		if(isset($this->theData["URI"]))
		{
			$return = $this->theData["URI"];
		}
		else
		{
			$return = null;
		}
		return $return;
	}
	
	/**
	 * pageBodyID function.
	 * 
	 * @brief Returns the body of the page
	 * @access public
	 * @return String with the body information, else null
	 */
	public function pageBodyID()
	{
		if(isset($this->theData["PageData"]))
		{
			$return = $this->theData["PageData"];
		}
		else
		{
			$return = null;
		}
		return $return;
	}
	
	/**
	 * pageID function.
	 * 
	 * @brief Simply returns the ID of the page, if the page is new then it returns -1
	 * @access public
	 * @return Integer of the ID of the page
	 */
	public function pageID()
	{
		if(isset($this->theData["PrimaryKey"]))
		{
			$return = $this->theData["PrimaryKey"];
		}
		else
		{
			$return = -1;
		}
		return $return;
	}
	
	public function categoryTitleID()
	{
		return "test";
	}
	
	public function categoryID()
	{
		return -1;
	}
	
	/**
	 * isDraft function.
	 * 
	 * @brief Returns if the page/post is a draft or not, defaults to being a draft if not set
	 * @access public
	 * @return Integer, 1=true, 0=false
	 */
	public function isDraft()
	{
		if(isset($this->theData["Draft"]))
		{
			$return = $this->theData["Draft"];
		}
		else
		{
			$return = 1;
		}
		
		return $return;
	}
	
	// for now we just say we never have an error
	public function themeError()
	{
		return false;
	}
	
	/**
	 * pageCorral function.
	 * 
	 * @brief Returns the corral that the page has
	 * @access public
	 * @return String with the Page's Corral name
	 */
	public function pageCorral()
	{
		$return = null;
		
		if(isset($this->theData["Corral"]))
		{
			$return = $this->theData["Corral"];
		}
		return $return;
	}
	
	public function haveNextPage()
	{
	
	}
	
	public function havePreviousPage()
	{
	
	}
	
	public function snippetTitleID()
	{
		$return = null;
		
		if(isset($this->theData["Name"]))
		{
			$return = $this->theData["Name"];
		}
		
		return $return;
	}
	
	public function snippetBodyID()
	{
		$return = null;
		
		if(isset($this->theData["SnippetData"]))
		{
			$return = $this->theData["SnippetData"];
		}
		
		return $return;
	}
	
	public function snippetID()
	{
		$return = -1;
		
		if(isset($this->theData["PrimaryKey"]))
		{
			$return = $this->theData["PrimaryKey"];
		}
		
		return $return;
	}
	
	public function getAdminURL()
	{
		static $address = null;
		
		if($address == null)
		{
			if(substr(F_ADMINADDRESS, -1) == '/')
			{
				$address = substr(F_ADMINADDRESS, 0, (strlen(F_ADMINADDRESS) - 1));
			}
			else
			{
				$address = F_ADMINADDRESS;
			}
		}
		
		return $address;
	}
}
?>