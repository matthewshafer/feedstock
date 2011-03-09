<?php

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
	
	public function __construct($router, $db, $themeData, $baseLoc, $themeName, $pageType $postsPerPage)
	{
		$this->router = $router;
		$this->database = $db;
		$this->themeData = $themeData;
		$this->baseLocation = $baseLoc;
		$this->themeName = $themeName;
		$this->pageType = $pageType;
		$this->postsPerPage = $postsPerPage;
		$this->themeLocation = $this->baseLocation . '/private/themes/' . $this->themeName;
	}
	
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
	
	public function templateFile()
	{
		$file = null;
		
		switch((string)$pageType)
		{
			case "":
				$file = "index.php";
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
	
	private function validThemeFile($file)
	{
		$return = false;
		$file = $this->themeLocation . "/" . $file;
		
		// checks to see if the file actually exists and is readable
		if(file_exists($file) && is_readable($file))
		{
			$return = true;
		}
		
		return $return;
	}
	
	private function loadDataForPostList()
	{
		$offset = $this->router->getPageOffset() * $this->postsPerPage;
		$this->templateData->addData($this->database->getPosts($this->postsPerPage, $offset));
	}
	
	// 0 for category 1 for tag
	private function figureCategoryOrTagInfo($which)
	{
		$file = $this->themeLocation . "/postList.php";
		
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
			// throws an exception if the third part of the uri is not "page" so if someone tries to do /category/test/arrrr/3
			// an error will be thrown
			if($this->router->getUriPosition(3) !== "page")
			{
				throw new exception("Invalid URI");
			}
			
			// page id of the page that is currently in the uri
			// so /category/something/page/2 would give pageId of 2
			$pageId = (int)$this->router->getUriPosition(4);
			
			if($pageId > 0)
			{
				$pageId = ($pageId - 1) * 10;
				
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
	
	private function figurePageOrPost()
	{
	
	}
}
?>