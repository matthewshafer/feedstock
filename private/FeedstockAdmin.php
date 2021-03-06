<?php
/**
* @file
* @author Matthew Shafer <matt@niftystopwatch.com>
* @brief Build's all the admin things.
* 
*/
class FeedstockAdmin
{
	private $templateEngine = null;
	private $templateLoader = null;
	private $postManager = null;
	private $databaseAdmin = null;
	private $cookieMonster = null;
	private $router = null;
	private $sitemapCreator = null;
	
	// config data array
	private $config = array();
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		require_once("../../config.php");
		
		require_once("includes/Router.php");
		
		$this->router = new Router($this->config['adminHtaccess'], $this->config['adminBase']);
		$this->router->buildRouting();
		
		require_once("includes/PostManager.php");
		
		$this->postManager = new PostManager();
		
		
		
		$this->databaseAdmin = $this->databaseMaker();
		
		require_once("includes/SiteUrlGenerator.php");
		$this->siteUrlGenerator = new SiteUrlGenerator($this->config['siteUrl'], $this->config['siteUrlBase'], $this->config['htaccess'], $this->router);
		
		require_once("includes/CookieMonster.php");
		
		$this->cookieMonster = new CookieMonster($this->databaseAdmin, $this->config['cookieName'], $this->config['siteUrl']);
		
		require_once("includes/SiteUrlGenerator.php");
		
		$siteUrlGenerator = new SiteUrlGenerator($this->config['adminAddress'], $this->config['adminBase'], $this->config['adminHtaccess'], $this->router);
		
		require_once("includes/TemplateEngineAdmin.php");
		
		// need to fix this to now support the new SiteUrlGenerator
		$this->templateEngine = new TemplateEngineAdmin($this->databaseAdmin, $this->router, $this->config['siteUrl'], $this->config['siteUrlBase'], $this->config['siteTitle'], $this->config['siteDescription'], $siteUrlGenerator->generateSiteUrl(), $this->config['baseLocation']);
		
		require_once("includes/OutputHelper.php");
		$outputHelper = new OutputHelper();
		
		require_once("includes/TemplateLoaderAdmin.php");
		$this->templateLoader = new TemplateLoaderAdmin($this->templateEngine, $outputHelper);
		
		if($this->config['generateSitemap'])
		{
			require_once("includes/SitemapCreator.php");
			$this->sitemapCreator = new SitemapCreator($this->databaseAdmin, $this->config['baseLocation'] . $this->config['sitemapPath'], $this->config['maxSitemapItems'], $this->siteUrlGenerator->generateSiteUrl(), $this->config['postsPerPage']);
		}
		
		$this->handleRequest();
	}
	
	private function handleRequest()
	{
		if($this->postManager->getPostType() === "login")
		{
			// check the login info and then set the cookie
			if($this->postManager->getPostByName("username") != null and $this->postManager->getPostByName("username") != null)
			{
				$userArray = $this->databaseAdmin->getUserByUserName($this->postManager->getPostByName("username"));
				//print_r($userArray);
				
				// this should be true if the person supplied the correct information
				if($userArray["PasswordHash"] === $this->makePasswordHash($this->postManager->getPostByName("password"), $userArray["Salt"], $this->config['passSalt']))
				{
					//echo "hit";
					$this->cookieMonster->createCookie($userArray["id"]);
				}
			}
		}
		
		if(!$this->cookieMonster->checkCookie())
		{
			// user is NOT logged in
			//echo $this->templateLoader->render();
			$this->templateLoader->render();
		
		}
		else
		{
			// so we want to check for post values first so we can process them
			// something like this should work
			if($this->postManager->havePostValues())
			{
				$this->handlePosts();
			}
			$this->templateEngine->loggedIn(true);
			//echo $this->templateLoader->render();
			// user is logged in
			$this->templateLoader->render();
		
		}
	}
	
	private function handlePosts()
	{
		switch($this->postManager->getPostType())
		{
			case "postAdd":
				echo "post Add";
				$this->addPost();
				break;
			case "postsRemove":
				echo "post Remove";
				$this->removePost();
				break;
			case "pageAdd":
				echo "page Add";
				$this->addPage();
				break;
			case "pageRemove":
				echo "page Remove";
				$this->removePage();
				break;
			case "categoryAdd":
				echo "category Add";
				$this->addCategory();
				break;
			case "categoryRemove":
				echo "category Remove";
				$this->removeCategory();
				break;
			// these next two are coming later as they they operate differently than categories (removing them is like categories though)
			// normally you have more tags than categories.
			case "tagAdd":
				echo "tag Add";
				break;
			case "tagRemove":
				echo "tag Remove";
				break;
			case "snippetAdd":
				echo "snipped Add";
				$this->addSnippet();
				break;
			case "snippetRemove":
				echo "snippet Remove";
				break;
		}
	}
	
	private function addPost()
	{
		//$postsNeeded = array("postTitle", "postorpagedata", "postCategories", "postTags", "draft", "id");
		$postsNeeded = array("postTitle", "postorpagedata", "draft", "id");
		
		if($this->databaseAdmin->isTransactional())
		{
			$this->databaseAdmin->startTransaction();
		}
		
		try
		{
			if($this->postManager->checkPostWithArray($postsNeeded))
			{
				//echo "<br>" . $this->generatePostUri($this->uriFriendlyTitle($this->postManager->getPostByName("postTitle")));
				//print_r($this->postManager->getPostByName("postCategories"));
				if($this->postManager->getPostByName("id") != -1)
				{
					$id = $this->postManager->getPostByName("id");
					// update
					
					$niceCheckedTitle = $this->checkAndFixNiceTitleCollision("post", $this->uriFriendlyTitle($this->postManager->getPostByName("postTitle")), $id);
					
					if($this->postManager->getPostByName("useCurrentDate") == 0)
					{
						$tempPostArray = $this->databaseAdmin->getPostDataById($id);
						
						$tempDate = null;
						
						if(isset($tempPostArray["Date"]))
						{
							$tempDate = strtotime($tempPostArray["Date"]);
						}
						
						$goodUri = $this->checkAndFixNiceUriCollision("post", $this->generatePostUri($this->uriFriendlyTitle($this->postManager->getPostByName("postTitle")), $tempDate), $id);
					}
					else
					{
						$goodUri = $this->checkAndFixNiceUriCollision("post", $this->generatePostUri($this->uriFriendlyTitle($this->postManager->getPostByName("postTitle"))), $id);
					}
					
					
					if($this->postManager->getPostByName("useCurrentDate") == 1)
					{
						$date = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
					}
					else
					{
						$date = null;
					}
					
					$this->databaseAdmin->addPost(
						$this->postManager->getPostByName("postTitle"), 
						$this->postManager->getPostByName("postorpagedata"), 
						$niceCheckedTitle, 
						$goodUri, 
						$this->cookieMonster->getUserID(), 
						$date, 
						$this->postManager->getPostByName("draft"), 
						$id
					);
				
					// only need to unlink updates
					$this->databaseAdmin->unlinkPostCategoriessAndTags($id);
					$this->databaseAdmin->processPostCategories($id, $this->postManager->getPostByName("postCategories"));
					$this->databaseAdmin->processTags($id, $this->tagsToArray());
					// commits the transaction. can be called even when not using a transactional engine.
					$this->databaseAdmin->commitTransaction();
				}
				else
				{
					//$this->tagsToArray();
					//echo "test";
					//echo "<br>" . $this->uriFriendlyTitle($this->postManager->getPostByName("postTitle"));
					$niceCheckedTitle = $this->checkAndFixNiceTitleCollision("post", $this->uriFriendlyTitle($this->postManager->getPostByName("postTitle")));
					// doing it this way allows to only have 1 of the same title and 1 of the same uri.  So if the user changes the structure we'll be fine
					$goodUri = $this->checkAndFixNiceUriCollision("post", $this->generatePostUri($this->uriFriendlyTitle($this->postManager->getPostByName("postTitle"))));
					
					$this->databaseAdmin->addPost(
						$this->postManager->getPostByName("postTitle"), 
						$this->postManager->getPostByName("postorpagedata"), 
						$niceCheckedTitle, 
						$goodUri, 
						$this->cookieMonster->getUserID(), 
						date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']), 
						$this->postManager->getPostByName("draft")
					);
					
					$id = $this->databaseAdmin->getPostIdNiceCheckedTitle($niceCheckedTitle);
					
					
					//print_r($this->postManager->getPostByName("postCategories"));
					$this->databaseAdmin->processPostCategories($id, $this->postManager->getPostByName("postCategories"));
					$this->databaseAdmin->processTags($id, $this->tagsToArray());
					// commits the transaction. can be called even when not using a transactional engine.
					$this->databaseAdmin->commitTransaction();
				}
				
				$this->purgeCache();
				
				if($this->config['feedPubSubHubBub'])
				{
					require_once("includes/feed/PubSubHubBub.php");
					
					$hub = new PubSubHubBub($this->config['feedPubSubHubBubPublishUrl'], $this->siteUrlGenerator->generateSiteUrl());
					$hub->publish();
					//$returned = $hub->publish();
					//echo "PubSub: ";
					//print_r($returned);
					//echo "\n";
				}
				
				if($this->sitemapCreator != null)
				{
					$this->sitemapCreator->generateSitemap();
				}
			}
		}
		catch(exception $e)
		{
			$this->databaseAdmin->rollbackTransaction();
		}
	}
	
	private function tagsToArray()
	{
		// this really doesnt error check so don't put a trailing , at the end
		$tempArr = array();
		$tempArr = explode(",", $this->postManager->getPostByName("postTags"));
		
		$tempArr2 = array();
		
		$tmpCt = count($tempArr);
		
		for($i = 0; $i < $tmpCt; $i++)
		{
			$tmp = array("Title" => $tempArr[$i], "NiceTitle" => $this->uriFriendlyTitle(trim($tempArr[$i])));
			
			$tempArr2[] = $tmp;
		}
		
		//print_r($tempArr2);
		//print_r($tempArr);
		
		return $tempArr2;
	}
	
	private function removePost()
	{
		$neededInfo = array("postsDelete");
		
		if($this->postManager->checkPostWithArray($neededInfo))
		{
			//print_r($this->postManager->getPostByName("postsDelete"));
			foreach($this->postManager->getPostByName("postsDelete") as $key)
			{
				$this->databaseAdmin->deletePost($key);
			}
			
			$this->purgeCache();
		}
		
		if($this->sitemapCreator != null)
		{
			$this->sitemapCreator->generateSitemap();
		}
	}
	
	private function addPage()
	{
		$pagesNeeded = array("pageTitle", "postorpagedata", "draft", "id");
		
		if($this->postManager->checkPostWithArray($pagesNeeded))
		{
			if($this->postManager->checkPostVal("pageCorral"))
			{
				$corral = $this->postManager->getPostByName("pageCorral");
				$corral = str_replace(array("\n", "\r", "\t", " ", "\O", "\xOB"), '', $corral);
				$corral = strtolower($corral);
			}
			else
			{
				$corral = "";
			}
			
			
			
			
			if($this->postManager->getPostByName("id") != -1)
			{
				//update
				$id = $this->postManager->getPostByName("id");
				$niceCheckedTitle = $this->checkAndFixNiceTitleCollision("page", $this->uriFriendlyTitle($this->postManager->getPostByName("pageTitle")), $id);
				
				if($this->postManager->getPostByName("pageUri") === "")
				{
					$nonCheckedUri = sprintf("/%s", $this->uriFriendlyTitle($this->postManager->getPostByName("pageTitle")));
				}
				else
				{	
					$nonCheckedUri = $this->uriFriendlyCustomEntered($this->postManager->getPostByName("pageUri"));
				}
				$goodUri = $this->checkAndFixNiceUriCollision("page", $nonCheckedUri, $id);
				
				$this->databaseAdmin->addPage(
				$this->postManager->getPostByName("pageTitle"), 
				$this->postManager->getPostByName("postorpagedata"), 
				$niceCheckedTitle, 
				$goodUri, 
				$this->cookieMonster->getUserID(), 
				null, 
				$this->postManager->getPostByName("draft"),
				$corral, 
				$id
				);
				
			}
			else
			{
				$niceCheckedTitle = $this->checkAndFixNiceTitleCollision("page", $this->uriFriendlyTitle($this->postManager->getPostByName("pageTitle")));
				// I can make this a bunch better
				
				if($this->postManager->getPostByName("pageUri") === "")
				{
					$nonCheckedUri = sprintf("/%s", $this->uriFriendlyTitle($this->postManager->getPostByName("pageTitle")));
				}
				else
				{
					$nonCheckedUri = $this->uriFriendlyCustomEntered($this->postManager->getPostByName("pageUri"));
					echo $nonCheckedUri;
				}
				$goodUri = $this->checkAndFixNiceUriCollision("page", $nonCheckedUri);
				
				$this->databaseAdmin->addPage(
				$this->postManager->getPostByName("pageTitle"), 
				$this->postManager->getPostByName("postorpagedata"), 
				$niceCheckedTitle, 
				$goodUri, 
				$this->cookieMonster->getUserID(), 
				date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']), 
				$this->postManager->getPostByName("draft"),
				$corral
				);
			}
			
			$this->purgeCache();
		}
		
		if($this->sitemapCreator != null)
		{
			$this->sitemapCreator->generateSitemap();
		}
	}
	
	
	private function removePage()
	{
		$neededItems = array("pageDelete");
		
		if($this->postManager->checkPostWithArray($neededItems))
		{
			foreach($this->postManager->getPostByName("pageDelete") as $key)
			{
				$this->databaseAdmin->removePage($key);
			}
			
			$this->purgeCache();
		}
		
		if($this->sitemapCreator != null)
		{
			$this->sitemapCreator->generateSitemap();
		}
	}
	
	private function addCategory()
	{
		$categoriesNeeded = array("categoryTitle", "id");
		
		if($this->postManager->checkPostWithArray($categoriesNeeded))
		{
			if($this->postManager->getPostByName("id") != -1)
			{
				// update
			}
			else
			{
				$niceTitle = $this->uriFriendlyTitle($this->postManager->getPostByName("categoryTitle"));
				
				$this->databaseAdmin->addCategory($this->postManager->getPostByName("categoryTitle"), $niceTitle);
			}
			
			$this->purgeCache();
		}
		
		if($this->sitemapCreator != null)
		{
			$this->sitemapCreator->generateSitemap();
		}
	}
	
	private function removeCategory()
	{
		
	}
	
	private function addSnippet()
	{
		$snippetNeeded = array("snippetTitle", "id", "postorpagedata");
		
		if($this->postManager->checkPostWithArray($snippetNeeded))
		{
			if($this->postManager->getPostByName("id") != -1)
			{
				$niceTitle = $this->uriFriendlyTitle($this->postManager->getPostByName("snippetTitle"));
				$niceTitle = $this->checkAndFixNiceTitleCollision("snippet", $niceTitle, $this->postManager->getPostByName("id"));
				$this->databaseAdmin->addSnippet($niceTitle, $this->postManager->getPostByName("postorpagedata"), $this->postManager->getPostByName("id"));
			}
			else
			{
				echo "here";
				$niceTitle = $this->uriFriendlyTitle($this->postManager->getPostByName("snippetTitle"));
				$niceTitle = $this->checkAndFixNiceTitleCollision("snippet", $niceTitle);
				$this->databaseAdmin->addSnippet($niceTitle, $this->postManager->getPostByName("postorpagedata"));
			}
		}
	}
	
	private function checkAndFixNiceTitleCollision($type, $niceTitle, $id = null)
	{
		$i = 1;
		$moreThanOne = true;
		$tmp = null;
		
		$moreThanOne = $this->databaseAdmin->checkDuplicateTitle($type, $niceTitle, $id);
		
		
		while(!$moreThanOne)
		{
			$tmp = $niceTitle . "-" . ($i + 1);
			//echo $tmp;
			$moreThanOne = $this->databaseAdmin->checkDuplicateTitle($type, $tmp, $id);
			
			$i++;
		}
		
		if($i > 1)
		{
			$niceTitle = $niceTitle . "-" . $i;
		}
		
		//echo $niceTitle;
		
		return $niceTitle;
	}
	
	/**
	 * checkAndFixNiceUriCollision function.
	 * 
	 * @brief fixes URI collisions. So if we have /test and we try to make another one it makes /test-2
	 * @access private
	 * @param mixed $type
	 * @param mixed $niceUri
	 * @param mixed $id. (default: null)
	 * @return String containing the no-collision URI
	 */
	private function checkAndFixNiceUriCollision($type, $niceUri, $id = null)
	{
		$i = 1;
		$moreThanOne = true;
		
		$moreThanOne = $this->databaseAdmin->checkDuplicateUri($type, $niceUri, $id);
		
		while(!$moreThanOne)
		{
			$tmp = $niceUri . "-" . ($i + 1);
			
			$moreThanOne = $this->databaseAdmin->checkDuplicateUri($type, $tmp, $id);
			
			$i++;
		}
		
		if($i > 1)
		{
			$niceUri = $niceUri . "-" . $i;
		}
		
		return $niceUri;
	}
	
	/**
	 * uriFriendlyTitle function.
	 * 
	 * @brief Makes a URI friendly title
	 * @access private
	 * @param mixed $title
	 * @return String that is the URI friendly title
	 */
	private function uriFriendlyTitle($title)
	{
		$return = null;
		if($title != null)
		{
			//echo $title;
			// slightly modified from http://cubiq.org/the-perfect-php-clean-url-generator/12
			$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower(trim($clean, '-'));
			$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
			
			$return = $clean;
		}
		else
		{
			// error, we should do something
		}
		
		return $return;
	}
	
	/**
	 * uriFriendlyCustomEntered function.
	 * 
	 * @brief makes a nice URI from a custom entered URI
	 * @access private
	 * @param mixed $title
	 * @return String that is the nice URI
	 */
	private function uriFriendlyCustomEntered($title)
	{
		$return = null;
		if($title != null)
		{
			$ct = strlen($title);
			if($ct > 0 && $title[0] != "/")
			{
				$title = sprintf("/%s", $title);
			}
			
			$ct = strlen($title);
			if($ct > 0 && $title[$ct - 1] === "/")
			{
				$title = substr($title, 0, -1);
			}
			//echo $title;
			// slightly modified from http://cubiq.org/the-perfect-php-clean-url-generator/12
			$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower(trim($clean, '-'));
			// this is changed so we dont strip out / like test/what
			$clean = preg_replace("/\s+/", '-', $clean);
			
			$return = $clean;
		}
		else
		{
			// error, we should do something
		}
		
		return $return;
	}
	
	/**
	 * generatePostUri function.
	 * 
	 * @access private
	 * @brief give us a nice title and we'll give you a full on uri for the post
	 * @param mixed $nice
	 * @param mixed $date. (default: null)
	 * @return String
	 */
	private function generatePostUri($nice, $date = null)
	{
		$temp = explode("/", $this->config['postFormat']);
		//print_r($temp);
		$tmpStr = "/";
		
		if($date === null)
		{
			$date = $_SERVER['REQUEST_TIME'];
		}
		
		$tmpCt = count($temp);
		
		for($i = 0; $i < $tmpCt; $i++)
		{
			switch((string)$temp[$i])
			{
				case "%MONTH%":
					$tmpStr .= date("m", $date) . "/";
					break;
				case "%DAY%":
					$tmpStr .= date("d", $date) . "/";
					break;
				case "%YEAR%":
					$tmpStr .= date("Y", $date) . "/";
					break;
				case "%TITLE%":
					$tmpStr .= $nice . "/";
					break;
					// CATEGORY DOESN't WORK
				//case "%CATEGORY%":
					//break;
			}
		}
		
		//if($tmpStr[strlen($tmpStr) - 1] == "/")
		if(substr_compare($tmpStr, '/', -1) === 0)
		{
			$tmpStr = substr($tmpStr, 0, -1);
		}
		
		return $tmpStr;
	}
	
	/**
	 * makePasswordHash function.
	 * 
	 * @brief Generates the password hash based on the password the user entered and the salt from the database
	 * @brief Grabs the salt from the config and mixes things up.  Uses Whirlpool hash function so it requires mcrypt
	 * @access private
	 * @param mixed $p
	 * @param mixed $s
	 * @return String that is the hash of the password
	 */
	private function makePasswordHash($p, $s, $s2)
	{	
		// create some var's we need for later
		$preSalt = null;
		$s2len = strlen($s2);
		$slen = strlen($s);
		$start = 0;
		
		// figure out which string is longer
		if($s2len < $slen)
		{
			$length = $slen;
		}
		else
		{
			$length = $s2len;
		}
		
		// mix up the two salt's into one new salt
		while($start < $length)
		{
			if($start < $slen)
			{
				$preSalt .= $s[$start];
			}
			
			if($start < $s2len)
			{
				$preSalt .= $s2[$start];
			}
			
			$start++;
		}
		
		// split up the password into two parts
		$password = str_split($p, (strlen($p)/2)+1);
		
		// same deal with the salt
		$salt = str_split($preSalt, (strlen($preSalt)/2)+1);
		
		// hash them using whirlpool with the salts added
		$hash = hash('whirlpool', $password[0].$salt[0].$password[1].$salt[1]);
		
		return $hash;
	}
	
	
	private function databaseMaker()
	{
		require_once("includes/interfaces/GenericDatabase.php");
		require_once("includes/interfaces/GenericDatabaseAdmin.php");
		require_once("includes/databases/" . $this->config['databaseType'] . "DatabaseAdmin.php");
		$return = null;
		$type = $this->config['databaseType'] . "DatabaseAdmin";
		

		$return = new $type($this->config['databaseUsername'], $this->config['databasePassword'], $this->config['databaseAddress'], $this->config['databasePort'], $this->config['databaseName'], $this->config['databaseTablePrefix']);

		
		return $return;
	}
	
	private function purgeCache()
	{
		if($this->config['cacheEnable'])
		{
				require_once("includes/CacherCreator.php");
				$this->cacherCreator = new CacherCreator($this->config['cacheName'], $this->config['cachePrefix'], $this->config['cacheExpireTime'], $this->config['baseLocation']);
			
			if($this->cacherCreator->createCacher())
			{
				$cache = $this->cacherCreator->getCacher();
				$cache->purgeCache();
			}
		}
	}
}
?>