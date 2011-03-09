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
				$file = $this->figureCategoryInfo();
			break;
			case "tag":
				$file = $this->figureTagInfo();
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
	
	private function figureCategoryInfo()
	{
		$file = $this->themeLocation . "/postList.php";
		
		// theses are currently commented because im testing hardcoding the variables so we don't have to search for the category in the array
		//$categoryNameOffset = $this->router->searchURI("category") + 1;
		//$categoryName = $this->router->getUriPosition($categoryNameOffset);
		
		// this is the above using hardcoded values
		$categoryName = $this->router->getUriPosition(2);
		
		
		// if the category does not exist in the database
		if(!$this->database->checkCategoryOrTagName($categoryName, 0))
		{
			throw new exception("Category does not exist");
		}
		
		// a length of two means that the uri (after the base is stripped) is /category/someNameHere
		if($this->router->uriLength() === 2)
		{
			$this->templateData->addData($this->database->getPostsInCategoryOrTag(0, $this->postsPerPage, 0));
		}
		// a length of two means that the uri (after the base is stripped) is /category/someNameHere/page/someNumberHere
		else if($this->router->uriLength() === 4)
		{
		
		}
		else
		{
			throw new exception("Invalid Category URI");
		}
	}
	
	private function figureTagInfo()
	{
	
	}
	
	private function figurePageOrPost()
	{
	
	}
}
?>