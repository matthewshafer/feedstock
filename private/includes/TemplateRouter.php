<?php

class TemplateRouter
{
	private $router;
	private $themeData;
	private $baseLocation;
	private $pageType;
	private $themeName;
	
	public function __construct($router, $themeData, $baseLoc, $themeName, $pageType)
	{
		$this->router = $router;
		$this->themeData = $themeData;
		$this->baseLocation = $baseLoc;
		$this->themeName = $themeName;
		$this->pageType = $pageType;
	}
	
	public function templateFile()
	{
		$return = $this->baseLocation . '/private/themes/' . $this->themeName;
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
		
		if(!$this->validThemeFile())
		{
			throw new exception("Missing required theme elements");
		}
	}
}
?>