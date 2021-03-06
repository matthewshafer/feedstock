<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Does all the heavy lifting for admin page themes
 * 
 */
class TemplateEngineAdmin
{
	protected $db;
	protected $router;
	protected $isLoggedIn = false;
	protected $theData = array();
	protected $theCategoryData = array();
	protected $theTagData = array();
	protected $htmlPageTitle = null;
	private $haveNextPage = false;
	private $count = -1;
	private $siteUrl = "";
	private $siteUrlBase = "";
	private $siteTitle = "";
	private $siteDescription = "";
	private $adminAddress = "";
	private $baseLocation = "";
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $database
	 * @param mixed $router
	 * @return void
	 */
	public function __construct($database, $router, $siteUrl, $siteUrlBase, $siteTitle, $siteDescription, $adminAddress, $baseLocation)
	{
		$this->db = $database;
		$this->router = $router;
		$this->siteUrl = $siteUrl;
		$this->siteUrlBase = $siteUrlBase;
		$this->siteTitle = $siteTitle;
		$this->siteDescription = $siteDescription;
		$this->adminAddress = $adminAddress;
		$this->baseLocation = $baseLocation;
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
	public function getThemeLocation()
	{
		return $this->request();
	}
	
	/**
	 * isThemeFileValid function.
	 * 
	 * @brief Checks admin theme files, only because I allow custom themes to be created otherwise I could do without this
	 * @access private
	 * @param mixed $file
	 * @return void
	 */
	private function isThemeFileValid($file)
	{
		// need to fix the part that says stock because what if you were using a different theme.
		$loc = $this->baseLocation . "/private/themesAdmin/" . "stock";
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
		$return = $this->baseLocation . "/private/themesAdmin/" . "stock";
		
		if($this->isLoggedIn)
		{
			$return .= $this->itsNotMeItsYou();
		}
		else
		{
			$return .= "/login.php";
			$this->htmlPageTitle = "login";
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
				$this->htmlPageTitle = "index";
				break;
			case "post":
				$return = "/createPost.php";
				$this->htmlPageTitle = "New Post";
				// set up some vars, we want to check if we have an ID in the uri and if we do we need to load that from the db and set up the vars
				if($this->router->getUriPosition(2) != null)
				{
					$this->theData = $this->db->getPostDataById((int)$this->router->getUriPosition(2));
					$tmpTitle = $this->postTitleID();
					
					if($tmpTitle != null)
					{
						$this->htmlPageTitle = sprintf("%s%s", $tmpTitle, " :: Post");
					}
				}
				$this->theCategoryData = $this->getCategoriesList((int)$this->router->getUriPosition(2));
				break;
			case "page":
				$return = "/createPage.php";
				$this->htmlPageTitle = "New Page";
				// set up some vars, we want to check if we have an ID in the uri and we need to load that and set up some stuff
				if($this->router->getUriPosition(2) != null)
				{
					$this->theData = $this->db->getPageDataById((int)$this->router->getUriPosition(2));
					$tmpTitle = $this->pageTitleID();
					
					if($tmpTitle != null)
					{
						$this->htmlPageTitle = sprintf("%s%s", $tmpTitle, " :: Page");
					}
				}
				break;
			case "posts":
				$return = "/postList.php";
				$this->htmlPageTitle = "Post List";
				// gonna want to set some stuff up
				//$this->theData = $this->db->getPostList();
				$this->theData = $this->getPostOrPageList("post");
				break;
			case "pages":
				$return = "/pageList.php";
				$this->htmlPageTitle = "Page List";
				// gonna want to set some stuff up
				$this->theData = $this->getPostOrPageList("page");
				break;
			case "categories":
				$return = "/categories.php";
				$this->htmlPageTitle = "Categories";
				$this->theCategoryData = $this->getCategoriesList();
				break;
			case "corral":
				$return = "/corral.php";
				$this->htmlPageTitle = "Corrals";
				$this->theData = $this->getCorrals();
				break;
			case "tags":
				$return = $this->figureTagPage();
				//$return = "/tags.php";
				//$this->theTagData = $this->getTagsList();
				break;
			case "snippets":
				$return = "/snippet.php";
				$this->htmlPageTitle = "Snippets";
				//need to get the data but i need to make a function first
				$this->theData = $this->db->getSnippetList();
				break;
			case "snippet":
				$return = "/createSnippet.php";
				$this->htmlPageTitle = "New Snippet";
				// also need to get data
				if($this->router->getUriPosition(2) != null)
				{
					$this->theData = $this->db->getSnippetById($this->router->getUriPosition(2));
					
					$tmpTitle = $this->snippetTitleID();
					
					if($tmpTitle != null)
					{
						$this->htmlPageTitle = sprintf("%s%s", $tmpTitle, " :: Snippet");
					}
				}
				break;
		}
		
		if($return === null)
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
		
		if($specificCorral === null)
		{
			$return = $this->db->getCorralList();
		}
		else
		{
			// going to get a specific corral
			$return = $this->db->getPagesInCorral($specificCorral);
			
			$this->htmlPageTitle = sprintf("%s%s", $specificCorral, " :: Corral");
		}
		
		return $return;
	}
	
	private function figureTagPage()
	{
		$return = null;
		
		if($this->router->uriLength() === 1)
		{
			$return = "/tags.php";
			$this->theTagData = $this->getTagsList();
			$this->htmlPageTitle = "Tags";
		}
		else if($this->router->uriLength() === 2)
		{
			$return = "/tagPosts.php";
			
			if($this->db->checkCategoryTagName((string)$this->router->getUriPosition(2), 1))
			{
				$this->theData = $this->db->getPostsInCategoryOrTag((string)$this->router->getUriPosition(2), 1, 99999, 0, true);
				//print_r($this->theData);
				
				$this->htmlPageTitle = sprintf("%s%s", $this->router->getUriPosition(2), " :: Tag");
			}
			else
			{
				$return = null;
			}
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
		$offset = (int)$this->router->getUriPosition(2);
		$tmpArr = array();
		// the one thing is if you do like /posts/omg it just sends you to the begining
		if($offset < 2 || $offset === null)
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
		
		
		if($type === "post")
		{
			//$tmpArr = $this->db->getPostList($limit, $offset);
			// this one is from the normal database class.  It get's us things like author
			$tmpArr = $this->db->getPosts($limit, $offset, true);
		}
		else
		{
			$tmpArr = $this->db->getPageList($limit, $offset);
		}
		
		if(count($tmpArr) === $limit)
		{
			$this->haveNextPage = true;
		}
		
		if(!isset($tmpArr[0]["PrimaryKey"]))
		{
			array_pop($tmpArr);
		}
		
		return $tmpArr;
	}
	
	public function haveNextPage()
	{
		return $this->haveNextPage;
	}
	
	public function haveNextPagesPageHtml($title = "Next Page ->")
	{
		$return = "";
		
		if($this->haveNextPage)
		{
			$pageNumber = $this->router->getUriPosition($this->router->uriLength()) + 1;
				
			if($pageNumber === 1)
			{
				$pageNumber = 2;
			}
				
				$return = sprintf('<a href="%s/pages/%d">%s</a>', $this->adminAddress, $pageNumber, $title);
		}
		
		return $return;
	}
	
	public function haveNextPostsPageHtml($title = "Next Page ->")
	{
		$return = "";
		
		if($this->haveNextPage)
		{
			$pageNumber = $this->router->getUriPosition($this->router->uriLength()) + 1;
			
			if($pageNumber === 1)
			{
				$pageNumber = 2;
			}
				
			$return = sprintf('<a href="%s/posts/%d">%s</a>', $this->adminAddress, $pageNumber, $title);
		}
		
		return $return;
	}
	
	public function havePreviousPage()
	{
		$return = false;
		
		$tempVal = $this->router->getUriPosition($this->router->uriLength());
		
		if($this->theData != null && is_int($tempVal) && (int)$tempVal > 1)
		{
			$return = true;
		}
		
		return $return;
	}
	
	public function havePreviousPagesPageHtml($title = "<- Previous Page")
	{
		$return = "";
		
		$tempVal = $this->router->getUriPosition($this->router->uriLength());
		
		if($this->theData != null && is_int((int)$tempVal) && (int)$tempVal > 1)
		{
			$pageNumber = $this->router->getUriPosition($this->router->uriLength()) - 1;
			
			$return = sprintf('<a href="%s/pages/%d">%s</a>', $this->adminAddress, $pageNumber, $title);
		}
		
		return $return;
	}
	
	public function havePreviousPostsPageHtml($title = "<- Previous Page")
	{
		$return = "";
		
		$tempVal = $this->router->getUriPosition($this->router->uriLength());
		
		if($this->theData != null && is_int((int)$tempVal) && (int)$tempVal > 1)
		{
			$pageNumber = $this->router->getUriPosition($this->router->uriLength()) - 1;
			
			$return = sprintf('<a href="%s/posts/%d">%s</a>', $this->adminAddress, $pageNumber, $title);
		}
		
		return $return;
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
					if($key2["CatTagID"] === $key["PrimaryKey"])
					{
						$found = true;
					}
				}
				
				$key["Checked"] = ($found === true ? 1 : 0);
				
				$tmpArr3[] = $key;
			}
			
			//print_r($tmpArr3);
			$tmpArr = $tmpArr3;
		}
		
		return $tmpArr;
	}
	
	private function getTagsList()
	{
		return $this->db->listCategoriesOrTags(1);
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
	
	public function getTagData()
	{
		return $this->theTagData;
	}
	
	/**
	 * postTitleId function.
	 * 
	 * @brief Simply returns the post title
	 * @access public
	 * @return String with the title of the post.  If it doesn't exist then null
	 */
	public function postTitleId()
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
	 * postBodyId function.
	 * 
	 * @brief Simply returns the post's body
	 * @access public
	 * @return String with the body of the post
	 */
	public function postBodyId()
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
	
	
	
	public function postCategoriesId()
	{
		return "cat1, cat2, cat3";
	}
	
	/**
	 * postTagsId function.
	 * 
	 * @brief Builds a correctly formatted tag string so tags can be added or removed by the user
	 * @access public
	 * @return String with the Tags else blank
	 */
	public function postTagsId()
	{
		if($this->router->getUriPosition(2) != null)
		{
			$arr = $this->db->getSinglePostTags((int)$this->router->getUriPosition(2));
			$return = implode(", ", $arr);
		}
		else
		{
			$return = "";
		}
		return $return;
	}
	
	/**
	 * postId function.
	 * 
	 * @brief Returns the ID of the post otherwise it returns -1 which stands for a new post
	 * @access public
	 * @return Integer that is the postID
	 */
	public function postId()
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
	 * pageTitleId function.
	 * 
	 * @brief Returns the title of the page
	 * @access public
	 * @return String with the title of the page, else null
	 */
	public function pageTitleId()
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
	 * pageUri function.
	 * 
	 * @brief Returns the URI for the page
	 * @access public
	 * @return String with the URI, else null
	 */
	public function pageUri()
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
	 * pageBodyId function.
	 * 
	 * @brief Returns the body of the page
	 * @access public
	 * @return String with the body information, else null
	 */
	public function pageBodyId()
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
	 * pageId function.
	 * 
	 * @brief Simply returns the ID of the page, if the page is new then it returns -1
	 * @access public
	 * @return Integer of the ID of the page
	 */
	public function pageId()
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
	
	public function categoryTitleId()
	{
		return "test";
	}
	
	public function categoryId()
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
	public function haveThemeError()
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
	public function getPageCorral()
	{
		$return = null;
		
		if(isset($this->theData["Corral"]))
		{
			$return = $this->theData["Corral"];
		}
		return $return;
	}
	
	
	public function snippetTitleId()
	{
		$return = null;
		
		if(isset($this->theData["Name"]))
		{
			$return = $this->theData["Name"];
		}
		
		return $return;
	}
	
	public function snippetBodyId()
	{
		$return = null;
		
		if(isset($this->theData["SnippetData"]))
		{
			$return = $this->theData["SnippetData"];
		}
		
		return $return;
	}
	
	public function snippetId()
	{
		$return = -1;
		
		if(isset($this->theData["PrimaryKey"]))
		{
			$return = $this->theData["PrimaryKey"];
		}
		
		return $return;
	}
	
	public function getAdminUrl()
	{
		static $url = null;
		
		if($url === null)
		{
			if(($i = strpos($this->adminAddress, "/index.php")) != false)
			{
				$url = substr($this->adminAddress, 0, $i);
			}
			else
			{
				$url = $this->adminAddress;
			}
		}
		
		return $url;
	}
	
	public function siteName()
	{
		return $this->siteTitle;
	}
	
	public function siteDescription()
	{
		return $this->siteDescription;
	}
	
	public function siteNameLink()
	{
		return sprintf('<a href="%s%s">%s</a>', $this->siteUrl, $this->siteUrlBase, $this->siteName());
	}
	
	public function getHtmlTitle()
	{
		return $this->htmlPageTitle;
	}
	
	public function tagPostName()
	{
		$return = null;
		
		//if(substr($this->htmlPageTitle, -3) === "Tag")
		if(substr_compare($this->htmlPageTitle, "Tag", -3) === 0)
		{
			$return = substr($this->htmlPageTitle, 0, -7);
		}
		
		return $return;
	}
}
?>