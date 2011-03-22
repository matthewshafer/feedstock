<?php


/**
 * FeedLoader class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * loads the feed and figures out if it is an rss or atom feed
 */
class FeedLoader
{
	private $router;
	private $feedEngine;
	private $templateEngine;
	private $outputHelper;

	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param Router $router
	 * @param OutputHelper $outputHelper
	 * @param FeedEngine $feedEngine
	 * @param TemplateEngine $templateEngine
	 * @return void
	 */
	public function __construct(Router $router, OutputHelper $outputHelper, FeedEngine $feedEngine, TemplateEngine $templateEngine)
	{
		$this->router = $router;
		$this->outputHelper = $outputHelper;
		$this->feedEngine = $feedEngine;
		$this->templateEngine = $templateEngine;
	}
	
	
	/**
	 * loadFeed function.
	 * 
	 * loads a feed and returns the data it generated
	 * @access public
	 * @return string string containing the output of the feed
	 */
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
	
	
	/**
	 * loadRss function.
	 * 
	 * @access private
	 * @return string string containing the output of the rss feed
	 */
	private function loadRss()
	{
		
		include("templates/rss.php");
	
		return $this->outputHelper->stopStoreFlushGetBuffer();
	}
	
	
	/**
	 * loadAtom function.
	 * 
	 * @access private
	 * @return string string containing the output of the atom feed
	 */
	private function loadAtom()
	{
		
		include("templates/atom.php");
		
		return $this->outputHelper->stopStoreFlushGetBuffer();
	}
	
	

}
?>