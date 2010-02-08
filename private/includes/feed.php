<?php

class feed
{
	private $db = null;
	private $router = null;
	private $pageData = null;
	private $feedType = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $db
	 * @param mixed $router
	 * @return void
	 */
	public function __construct($db, $router)
	{
		$this->db = $db;
		$this->router = $router;
		$this->feedType = $this->router->getUriPosition(2);
		$this->pageData = $this->db->getPosts(0);
	}
	
	/**
	 * render function.
	 * 
	 * @brief generates the feed to either RSS 2.0 or ATOM. Should be valid for either one
	 * @access public
	 * @return void
	 */
	public function render()
	{
		ob_start();
		
		// need a last build date
		if($this->feedType == "" || $this->feedType == "rss" && $this->router->uriLength() <= 2)
		{
			echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			echo "\t" . '<rss version="2.0">' . "\n";
			echo "\t\t" . '<channel>' . "\n";
			echo "\t\t\t" . '<title>' . V_SITETITLE . '</title>' . "\n";
			echo "\t\t\t" . '<link>' . V_URL . '</link>' . "\n";
			echo "\t\t\t" . '<description>' . V_DESCRIPTION . '</description>' . "\n";
			echo "\t\t\t" . '<lastBuildDate>' . date("r", strtotime($this->pageData[0]["Date"])) . '</lastBuildDate>' . "\n";
			echo "\t\t\t" . '<language>en-us</language>' . "\n";
				
			foreach($this->pageData as $key)
			{
				echo "\t\t\t" . '<item>' . "\n";
				echo "\t\t\t\t" . '<title>' . $key["Title"] .'</title>' . "\n";
				echo "\t\t\t\t" . '<link>' . substr(V_URL, 0, strlen(V_URL) - 1) . $key["URI"] . '</link>' . "\n";
				echo "\t\t\t\t" . '<guid>' . substr(V_URL, 0, strlen(V_URL) - 1) . $key["URI"] . '</guid>' . "\n";
				echo "\t\t\t\t" . '<pubDate>' . date("r", strtotime($key["Date"])) . '</pubDate>' . "\n";
				echo "\t\t\t\t" . '<description>[CDATA[ ' . $key["PostData"] . ']]</description>' . "\n";
				echo "\t\t\t" . '</item>' . "\n";
			}
				
			echo "\t\t" . '</channel>' . "\n";
			echo "\t" . '</rss>';
				
		}
		else if($this->feedType == "atom" && $this->router->uriLength() <= 2)
		{
			echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
			echo "\t" . '<feed xmlns="http://www.w3.org/2005/Atom">' . "\n";
			echo "\t\t" . '<id>' . V_URL . 'feed/atom</id>' . "\n";
			echo "\t\t" . '<title>' . V_SITETITLE . '</title>' . "\n";
			echo "\t\t" . '<updated>' . date("c", strtotime($this->pageData[0]["Date"])) . '</updated>' . "\n";
			echo "\t\t" . '<link rel="self" href="' . V_URL . 'feed/atom/" type="application/atom+xml" />' . "\n";
			
			echo "\t\t" . '<author>' . "\n";
			echo "\t\t\t" . '<name>matt</name>' . "\n";
			echo "\t\t\t" . '<uri>http://niftystopwatch.com</uri>' . "\n";
			echo "\t\t\t" . '<email>matt@niftystopwatch.com</email>' . "\n";
			echo "\t\t" . '</author>';
			
			foreach($this->pageData as $key)
			{
				echo "\t\t" . '<entry>' . "\n";
				echo "\t\t\t" . '<title>' . $key["Title"] .'</title>' . "\n";
				echo "\t\t\t" . '<id>' . substr(V_URL, 0, strlen(V_URL) - 1) . $key["URI"] . '</id>' . "\n";
				echo "\t\t\t" . '<published>' . date("c", strtotime($key["Date"])) . '</published>' . "\n";
				echo "\t\t\t" . '<updated>' . date("c", strtotime($key["Date"])) . '</updated>' . "\n";
				echo "\t\t\t" . '<link href="' . substr(V_URL, 0, strlen(V_URL) - 1) . $key["URI"] . '"/>' . "\n";
				echo "\t\t\t" . '<summary>'. $key["PostData"] . '</summary>' . "\n";
				echo "\t\t\t" . '<content>'. $key["PostData"] . '</content>' . "\n";
				echo "\t\t" . '</entry>' . "\n";
			}
			
			echo '</feed>';
		}
		return ob_get_clean();
	}


}


?>