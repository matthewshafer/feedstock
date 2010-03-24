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
		
		require_once("includes/" . V_DATABASE . "Admin.php");
		
		$this->dbAdmin = new databaseAdmin($this->username, $this->password, $this->address, $this->database, $this->tableprefix);
		
		require_once("includes/cookieMonster.php");
		
		$this->cookieMonster = new cookieMonster($this->dbAdmin);
		
		require_once("includes/templateEngineAdmin.php");
		
		$this->templateEngine = new templateEngineAdmin($this->database, $this->router);
		
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
			case "postRemove":
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
				// update
				
				
				//$this->dbAdmin->addPost($this->postManager->getPostByName("postTitle"), $this->postManager->getPostByName("postorpagedata"), $niceTitle, $uri, $author, $date, $category, $tags, $draft, $id);
			}
			else
			{
				//echo "test";
				//echo "<br>" . $this->uriFriendlyTitle($this->postManager->getPostByName("postTitle"));
				$this->dbAdmin->addPost(
				$this->postManager->getPostByName("postTitle"), 
				$this->postManager->getPostByName("postorpagedata"), 
				$this->uriFriendlyTitle($this->postManager->getPostByName("postTitle")), 
				$this->generatePostUri($this->uriFriendlyTitle($this->postManager->getPostByName("postTitle"))), 
				$this->cookieMonster->getUserID(), 
				date("Y-m-d H:i:s", time()), 
				$this->postManager->getPostByName("postCategories"), 
				$this->postManager->getPostByName("postTags"), 
				$this->postManager->getPostByName("draft")
				);
			}
		}
	}
	
	private function removePost()
	{
	
	}
	
	private function addPage()
	{
	
	}
	
	private function removePage()
	{
	
	}
	
	private function addCategory()
	{
	
	}
	
	private function removeCategory()
	{
		
	}
	
	private function checkAndFixNiceTitleCollision($niceTitle)
	{
		
	}
	
	
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
		print_r($temp);
		$tmpStr = "/";
		
		if($date == null)
		{
			$date = time();
		}
		
		for($i = 0; $i < count($temp); $i++)
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

}
?>