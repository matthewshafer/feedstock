<?php

class FeedLoader
{
	private $router;
	private $feedEngine;
	private $templateEngine;
	private $outputHelper;

	public function __construct($router, $outputHelper, $feedEngine, $templateEngine)
	{
		$this->router = $router;
		$this->outputHelper = $outputHelper;
		$this->feedEngine = $feedEngine;
		$this->templateEngine = $templateEngine;
	}
	
	public function loadFeed()
	{
		$type = $this->router->getUriPosition(2);
		$ret;
		
		if($type === null || $type === "rss")
		{
			$ret = $this->loadRss();
		}
		else if($type === "atom")
		{
			$ret = $this->loadAtom();
		}
		else
		{
			throw new Exception("Invalid Feed Type");
		}
		
		return $ret;
	}
	
	private function loadRss()
	{
		
		include("templates/rss.php");
	
		return $this->outputHelper->stopStoreFlushGetBuffer();
	}
	
	private function loadAtom()
	{
		
		include("templates/atom.php");
		
		return $this->outputHelper->stopStoreFlushGetBuffer();
	}
	
	

}
?>