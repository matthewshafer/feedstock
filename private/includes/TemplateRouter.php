<?php


/**
 * TemplateRouter class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * Figures out what needs to be loaded from the uri, loads that and stores it in TemplateData
 *
 */
class TemplateRouter
{
	private $router;
	private $database;
	private $themeData;
	private $baseLocation;
	private $pageType;
	private $themeName;
	private $themeLocation;
	private $postsPerPage;
	private $postFormat;
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param Router $router
	 * @param GenericDatabase $db
	 * @param TemplateData $templateData
	 * @param mixed $baseLoc
	 * @param mixed $themeName
	 * @param mixed $pageType
	 * @param mixed $postsPerPage
	 * @param mixed $postFormat
	 * @return void
	 */
	public function __construct(Router $router, GenericDatabase $db, TemplateData $templateData, $baseLoc, $themeName, $pageType, $postsPerPage, $postFormat)
	{
		$this->router = $router;
		$this->database = $db;
		$this->templateData = $templateData;
		$this->baseLocation = $baseLoc;
		$this->themeName = $themeName;
		$this->pageType = $pageType;
		$this->postsPerPage = $postsPerPage;
		$this->postFormat = $postFormat;
		$this->themeLocation = $this->baseLocation . '/private/themes/' . $this->themeName;
	}
	
	
	/**
	 * valid404Page function.
	 * 
	 * @access public
	 * @param mixed &$found
	 * @return string string containing the 404.php location
	 */
	public function valid404Page(&$found)
	{
		if($this->validThemeFile("404.php"))
		{
			$found = true;
		}
		else
		{
			$found = false;
		}
		
		return $this->themeLocation . "/" . "404.php";
	}
	
	
	/**
	 * templateFile function.
	 * 
	 * @access public
	 * @return string string containing the location of the theme file
	 */
	public function templateFile()
	{
		$file = null;
		
		switch((string)$this->pageType)
		{
			case "":
				$file = "index.php";
				// adding a null data so we don't run into any errors when loading the index page
				// the TemplateEngine loads the data because index pages don't require loading posts, only if the user's theme wants to
				$this->templateData->addData(null);
			break;
			case "page":
				$file = "postList.php";
				$this->loadDataForPostList();
			break;
			case "category":
				$file = $this->figureCategoryOrTagInfo(0);
			break;
			case "tag":
				$file = $this->figureCategoryOrTagInfo(1);
			break;
			default:
				$file = $this->figurePageOrPost();
			break;
		}
		
		if(!$this->validThemeFile($file))
		{
			throw new exception("Missing required theme elements");
		}
		
		return $this->themeLocation . "/" . $file;
	}
	
	
	/**
	 * validThemeFile function.
	 * 
	 * @access private
	 * @param mixed $file
	 * @return boolean true if the theme file exists, false if it doesn't
	 */
	private function validThemeFile($file)
	{
		$return = false;
		$file = $this->themeLocation . "/" . $file;
		
		// used for debugging
		//printf("%s\n\n", $file);
		
		// checks to see if the file actually exists and is readable
		if(file_exists($file) && is_readable($file))
		{
			$return = true;
		}
		
		return $return;
	}
	
	
	/**
	 * loadDataForPostList function.
	 * 
	 * @access private
	 * @return void
	 */
	private function loadDataForPostList()
	{
		$offset = $this->router->getPageOffset() * $this->postsPerPage;
		$this->templateData->addData($this->database->getPosts($this->postsPerPage, $offset));
	}
	
	
	/**
	 * figureCategoryOrTagInfo function.
	 *
	 * figures out if the category or tag exists. $which is either 0 for a category or 1 for a tag
	 * @access private
	 * @param mixed $which
	 * @return string string containing the file to load
	 */
	private function figureCategoryOrTagInfo($which)
	{
		$file = "postList.php";
		
		// theses are currently commented because im testing hardcoding the variables so we don't have to search for the category in the array
		//$categoryNameOffset = $this->router->searchURI("category") + 1;
		//$categoryName = $this->router->getUriPosition($categoryNameOffset);
		
		// this is the above using hardcoded values
		$name = $this->router->getUriPosition(2);
		
		
		// if the category does not exist in the database
		if(!$this->database->checkCategoryOrTagName($name, $which))
		{
			throw new exception("Category does not exist");
		}
		
		// a length of two means that the uri (after the base is stripped) is /category/someNameHere
		if($this->router->uriLength() === 2)
		{
			// when we check if it exists the database caches the category/tag for us which is why there is no need to send it the name again
			$this->templateData->addData($this->database->getPostsInCategoryOrTag($which, $this->postsPerPage, 0));
		}
		// a length of two means that the uri (after the base is stripped) is /category/someNameHere/page/someNumberHere
		else if($this->router->uriLength() === 4)
		{
			
			$pageId = -1;
		
			try
			{
				$pageId = $this->router->getPageOffset();
			}
			catch(exception $e)
			{
				throw new exception("Invalid URI");
			}
			
			if($pageId > 0)
			{
				$pageId = $pageId * $this->postsPerPage;
				
				$this->templateData->addData($this->database->getPostsInCategoryOrTag($which, $this->postsPerPage, $pageId));
			}
			else
			{
				throw new exception("Invalid Page Number");
			}
		}
		else
		{
			throw new exception("Invalid URI");
		}
		
		// returns the name of the theme file to load
		return $file;
	}
	
	
	/**
	 * figurePageOrPost function.
	 * 
	 * @access private
	 * @return string string containing the theme file to load
	 */
	private function figurePageOrPost()
	{
		$file;
		
		if($this->uriLooksLikePost())
		{
			// lookup the post
			$tmp = $this->database->getSinglePost($this->router->fullURI());
			
			// throw exception if post does not exist
			if(!isset($tmp[0]["PrimaryKey"]))
			{
				throw new exception("Post does not exist");
			}
			
			$this->templateData->addData($tmp);
			
			// default file for posts
			$file = "single.php";
		}
		else
		{
			// look for a page
			$tmp = $this->database->getPage($this->router->fullURI());
			
			if(empty($tmp))
			{
				throw new exception("Page does not exist");
			}
			
			$this->templateData->addData($tmp);
			
			// checking to see if the page uses a custom theme file
			if($tmp[0]["themeFile"] !== "")
			{
				$file = $tmp[0]["themeFile"] . ".php";
			}
			else
			{
				$file = "page.php";
			}
		}
		
		return $file;
	}
	
	
	/**
	 * uriLooksLikePost function.
	 * 
	 * tries to figure out if the uri is a post or not
	 * @access private
	 * @return boolean true if the uri is a post or false if it is not
	 */
	private function uriLooksLikePost()
	{
		$postFormatArray = explode("/", $this->postFormat);
		$return = true;
		$postFormatCt = count($postFormatArray);
		
		if($this->router->uriLength() !== $postFormatCt)
		{
			// return false if the length of the uri does not match that of the length of the post format
			$return = false;
		}
		// the uri could be a post because of its length
		else
		{
			$loopCt = 0;
			
			// while the return value is still true we loop, otherwise we end the loop as the return is set to false
			// the return is if the uri matches the post format
			while($loopCt < $postFormatCt && $return)
			{
				$uriPositionValue = $this->router->getUriPosition($loopCt + 1);
				
				switch((string)$postFormatArray[$loopCt])
				{
					case "%MONTH%":
						if((int)$uriPositionValue > 12 || (int)$uriPositionValue < 1)
						{
							$return = false;
						}
						break;
					case "%DAY%":
						if((int)$uriPositionValue > 31 || (int)$uriPositionValue < 1)
						{
							$return = false;
						}
						break;
					case "%YEAR%":
						if(strlen($uriPositionValue) < 4 || (int)$uriPositionValue < 1000)
						{
							$return = false;
						}
						break;
					case "%TITLE%":
						if($uriPositionValue === null)
						{
							$return = false;
						}
						break;
					default:
						$return = false;
						break;
				}
				
				++$loopCt;
			}
		}
		
		return $return;
	}
}
?>