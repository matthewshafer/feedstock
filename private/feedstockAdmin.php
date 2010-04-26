<?php
/**
* @file
* @author Matthew Shafer <matt@niftystopwatch.com>
* @brief Build's all the admin things.
* 
*/
class feedstockAdmin
{
	private $username = null;
	private $password = null;
	private $address = null;
	private $database = null;
	private $tableprefix = null;
	private $templateEngine = null;
	private $templateLoader = null;
	private $postManager = null;
	private $dbAdmin = null;
	private $cookieMonster = null;
	private $router = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		require_once("../../config.php");
		$this->address = $address;
		$this->password = $password;
		$this->username = $username;
		$this->database = $database;
		$this->tableprefix = $tableprefix;
		
		require_once("includes/router.php");
		
		$this->router = new router(F_ADMINHTACCESS, F_ADMINBASE);
		
		require_once("includes/postManager.php");
		
		$this->postManager = new postManager();
		
		//require_once("includes/" . V_DATABASE . "Admin.php");
		
		//$this->dbAdmin = new databaseAdmin($this->username, $this->password, $this->address, $this->database, $this->tableprefix);
		
		$this->dbAdmin = $this->databaseMaker();
		
		require_once("includes/cookieMonster.php");
		
		$this->cookieMonster = new cookieMonster($this->dbAdmin);
		
		require_once("includes/templateEngineAdmin.php");
		
		$this->templateEngine = new templateEngineAdmin($this->dbAdmin, $this->router);
		
		require_once("includes/templateLoader.php");
		$this->templateLoader = new templateLoader($this->templateEngine);
		
		if($this->postManager->getPostType() == "login")
		{
			// check the login info and then set the cookie
			if($this->postManager->getPostByName("username") != null and $this->postManager->getPostByName("username") != null)
			{
				$userArray = $this->dbAdmin->getUserByUserName($this->postManager->getPostByName("username"));
				//print_r($userArray);
				
				// this should be true if the person supplied the correct information
				if($userArray["PasswordHash"] == $this->makePasswordHash($this->postManager->getPostByName("password"), $userArray["Salt"]))
				{
					//echo "hit";
					$this->cookieMonster->createCookie($userArray["id"]);
				}
			}
		}
		
		if(!$this->cookieMonster->checkCookie())
		{
			// user is NOT logged in
			echo $this->templateLoader->render();
		
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
			echo $this->templateLoader->render();
			// user is logged in
		
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
		
		if($this->postManager->checkPostWithArray($postsNeeded))
		{
			//echo "<br>" . $this->generatePostUri($this->uriFriendlyTitle($this->postManager->getPostByName("postTitle")));
			//print_r($this->postManager->getPostByName("postCategories"));
			if($this->postManager->getPostByName("id") != -1)
			{
				$id = $this->postManager->getPostByName("id");
				// update
				
				$niceCheckedTitle = $this->checkAndFixNiceTitleCollision("post", $this->uriFriendlyTitle($this->postManager->getPostByName("postTitle")), $id);
				$goodUri = $this->checkAndFixNiceUriCollision("post", $this->generatePostUri($this->uriFriendlyTitle($this->postManager->getPostByName("postTitle"))), $id);
				
				$this->dbAdmin->addPost(
				$this->postManager->getPostByName("postTitle"), 
				$this->postManager->getPostByName("postorpagedata"), 
				$niceCheckedTitle, 
				$goodUri, 
				$this->cookieMonster->getUserID(), 
				null, 
				$this->postManager->getPostByName("draft"), 
				$id);
				
				// only need to unlink updates
				$this->dbAdmin->unlinkPostCatsAndTags($id);
				$this->dbAdmin->processPostCategories($id, $this->postManager->getPostByName("postCategories"));
				$this->dbAdmin->processTags($id, $this->tagsToArray());
			}
			else
			{
				//$this->tagsToArray();
				//echo "test";
				//echo "<br>" . $this->uriFriendlyTitle($this->postManager->getPostByName("postTitle"));
				$niceCheckedTitle = $this->checkAndFixNiceTitleCollision("post", $this->uriFriendlyTitle($this->postManager->getPostByName("postTitle")));
				// doing it this way allows to only have 1 of the same title and 1 of the same uri.  So if the user changes the structure we'll be fine
				$goodUri = $this->checkAndFixNiceUriCollision("post", $this->generatePostUri($this->uriFriendlyTitle($this->postManager->getPostByName("postTitle"))));
				
				$this->dbAdmin->addPost(
				$this->postManager->getPostByName("postTitle"), 
				$this->postManager->getPostByName("postorpagedata"), 
				$niceCheckedTitle, 
				$goodUri, 
				$this->cookieMonster->getUserID(), 
				date("Y-m-d H:i:s", time()), 
				$this->postManager->getPostByName("draft")
				);
				
				$id = $this->dbAdmin->getPostIDNiceCheckedTitle($niceCheckedTitle);
				
				
				//print_r($this->postManager->getPostByName("postCategories"));
				$this->dbAdmin->processPostCategories($id, $this->postManager->getPostByName("postCategories"));
				$this->dbAdmin->processTags($id, $this->tagsToArray());
			}
			
			$this->purgeCache();
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
			
			array_push($tempArr2, $tmp);
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
				$this->dbAdmin->deletePost($key);
			}
			
			$this->purgeCache();
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
				
				if($this->postManager->getPostByName("pageUri") == "")
				{
					$nonCheckedUri = sprintf("/%s", $this->uriFriendlyTitle($this->postManager->getPostByName("pageTitle")));
				}
				else
				{	
					$nonCheckedUri = $this->uriFriendlyCustomEntered($this->postManager->getPostByName("pageUri"));
				}
				$goodUri = $this->checkAndFixNiceUriCollision("page", $nonCheckedUri, $id);
				
				$this->dbAdmin->addPage(
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
				
				if($this->postManager->getPostByName("pageUri") == "")
				{
					$nonCheckedUri = sprintf("/%s", $this->uriFriendlyTitle($this->postManager->getPostByName("pageTitle")));
				}
				else
				{
					$nonCheckedUri = $this->uriFriendlyCustomEntered($this->postManager->getPostByName("pageUri"));
					echo $nonCheckedUri;
				}
				$goodUri = $this->checkAndFixNiceUriCollision("page", $nonCheckedUri);
				
				$this->dbAdmin->addPage(
				$this->postManager->getPostByName("pageTitle"), 
				$this->postManager->getPostByName("postorpagedata"), 
				$niceCheckedTitle, 
				$goodUri, 
				$this->cookieMonster->getUserID(), 
				date("Y-m-d H:i:s", time()), 
				$this->postManager->getPostByName("draft"),
				$corral
				);
			}
			
			$this->purgeCache();
		}
	}
	
	
	private function removePage()
	{
		$neededItems = array("pageDelete");
		
		if($this->postManager->checkPostWithArray($neededItems))
		{
			foreach($this->postManager->getPostByName("pageDelete") as $key)
			{
				$this->dbAdmin->removePage($key);
			}
			
			$this->purgeCache();
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
				
				$this->dbAdmin->addCategory($this->postManager->getPostByName("categoryTitle"), $niceTitle);
			}
			
			$this->purgeCache();
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
				$this->dbAdmin->addSnippet($niceTitle, $this->postManager->getPostByName("postorpagedata"), $this->postManager->getPostByName("id"));
			}
			else
			{
				echo "here";
				$niceTitle = $this->uriFriendlyTitle($this->postManager->getPostByName("snippetTitle"));
				$niceTitle = $this->checkAndFixNiceTitleCollision("snippet", $niceTitle);
				$this->dbAdmin->addSnippet($niceTitle, $this->postManager->getPostByName("postorpagedata"));
			}
		}
	}
	
	private function checkAndFixNiceTitleCollision($type, $niceTitle, $id = null)
	{
		$i = 1;
		$moreThanOne = true;
		$temp = null;
		
		$moreThanOne = $this->dbAdmin->checkDuplicateTitle($type, $niceTitle, $id);
		
		
		while(!$moreThanOne)
		{
			$tmp = $niceTitle . "-" . ($i + 1);
			//echo $tmp;
			$moreThanOne = $this->dbAdmin->checkDuplicateTitle($type, $tmp, $id);
			
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
		
		$moreThanOne = $this->dbAdmin->checkDuplicateURI($type, $niceUri, $id);
		
		while(!$moreThanOne)
		{
			$tmp = $niceUri . "-" . ($i + 1);
			
			$moreThanOne = $this->dbAdmin->checkDuplicateURI($type, $tmp, $id);
			
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
		$temp = explode("/", V_POSTFORMAT);
		//print_r($temp);
		$tmpStr = "/";
		
		if($date == null)
		{
			$date = time();
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
		
		if($tmpStr[strlen($tmpStr) - 1] == "/")
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
	private function makePasswordHash($p, $s)
	{	
		// create some var's we need for later
		$s2 = F_PSALT;
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
		require_once("includes/" . V_DATABASE . "Admin.php");
		$return = null;
		
		switch(V_DATABASE)
		{
			case "mysqli":
				$return = new mysqliDatabaseAdmin($this->username, $this->password, $this->address, $this->database, $this->tableprefix);
			break;
			case "mysql":
				$return = new mysqlDatabaseAdmin($this->username, $this->password, $this->address, $this->database, $this->tableprefix);
			break;
		}
		
		return $return;
	}
	
	private function purgeCache()
	{
		if(V_CACHE)
		{
			require_once("includes/cacheHandler.php");
			$cacheHandler = new cacheHandler($this->router);
			
			if($cacheHandler->cacheWriteableLoc())
			{
				$cache = $cacheHandler->cacheMaker();
				$cache->purgeCache();
			}
		}
	}
}
?>