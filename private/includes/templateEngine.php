<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Does all the heavy lifting for themes
 * 
 */
class templateEngine
{

	private $database = null;
	private $router = null;
	private $pageData = null;
	private $postCategory = null;
	private $postTag = null;
	private $arrayPosition = -1;
	private $errorText = null;
	private $themeValidError = false;
	
	/**
	 * __construct function.
	 * 
	 * @brief you need at least a 404.php for the theme to be valid
	 * @access public
	 * @param mixed $database
	 * @param mixed $router
	 * @return void
	 */
	public function __construct($database, $router)
	{
		$this->database = $database;
		$this->router = $router;
		
		// I don't think we need this any more since we print an error if themes dont exist so im going to comment it out.  if we need it i'll un comment it.
		/*
		if(!$this->themeFileIsValid("404.php"))
		{
			// really need to fix this. It'll probably happen when I refactor the template engine
			die("no valid theme file found. You have no 404.php");
		}
		*/
	}
	
	/**
	 * getThemeLoc function.
	 * 
	 * @brief simply calls our private function which should return the location of the theme file
	 * @access public
	 * @return void
	 */
	public function getThemeLoc()
	{
		return $this->request();	
	}
	
	/**
	 * themeFileIsValid function.
	 * 
	 * @access private
	 * @param mixed $file
	 * @return void
	 */
	private function themeFileIsValid($file)
	{
		$loc = V_BASELOC . "/private/themes/" . V_THEME;
		$return = null;
		
		if(file_exists($loc . "/" . $file) && is_readable($loc . "/" . $file))
		{
			$return = true;
		}
		else
		{
			$return = false;
		}
		
		return $return;
	}


	/**
	 * request function.
	 * 
	 * @access private
	 * @return void
	 */	
	private function request()
	{
		$return = V_BASELOC . "/private/themes/" . V_THEME;
		$file = null;
		
		if(strtolower($this->router->pageType()) == "")
		{	
			$file = "/index.php";
		}
		else if(strtolower($this->router->pageType()) == "page")
		{
			$offset = $this->router->getPageOffset() * 10;
			$this->pageData = $this->database->getPosts($offset);
			$file = "/postList.php";
		}
		else
		{
			
			if($this->checkUriPost())
			{
				$this->pageData = $this->database->getSinglePost($this->router->fullURI());
				
				
				if($this->pageData != null)
				{
					$this->arrayPosition = 0;
					$file = $this->arrayCustomFile("single");
				}
				else
				{
					$this->errorText .= "Post not Found";
				}
			}
			else
			{
				if($this->router->pageType() == "category")
				{
					echo "win!";
					
					echo $this->router->fullURI();
					
					// we could possibly make this a private function that allows us to grab the data, compare it and then return true or false and set the data to a global variable
 					if($this->database->checkCategoryTagName($this->router->getUriPosition($this->router->uriLength()), 0))
 					{
						//echo "true";
						$this->pageData = $this->database->getPostsInCategoryOrTag($this->router->getUriPosition($this->router->uriLength()), 0);
						//print_r($this->pageData);
					}
					else if($this->router->uriLength() == 1)
					{
						//echo "cool";
						$this->pageData = $this->database->listCategoriesOrTags(0);
					}
					else
					{
						//echo "false";
						$this->errorText = "Category Not Found";
						
					}		
					//$this->pageData = $this->database->getSpecificCategory($this->router->getUriPosition(2), $this->router->getPageOffset() * 10);
					// need some error checking for null pagedata
					if($this->pageData != null)
					{
						// just here for default
						if($this->router->uriLength() > 1)
						{
							$file = "/postList.php";
						}
						else
						{
							$file = "/category.php";
						}
					}
					else
					{
						// do stuff
					}
			
				}
				else if($this->router->pageType() == "tag")
				{
					if($this->database->checkCategoryTagName($this->router->getUriPosition($this->router->uriLength()), 1))
 					{
						//echo "true";
						$this->pageData = $this->database->getPostsInCategoryOrTag($this->router->getUriPosition($this->router->uriLength()), 1);
						//print_r($this->pageData);
					}
					else
					{
						//echo "false";
						$this->errorText = "Tag Not Found";
						
					}
					// need some error checking for null pagedata
					if($this->pageData != null)
					{
						// just here for default
						
						if($this->router->uriLength() > 1)
						{
							$file = "/postList.php";
						}
						else
						{
							$file = "/tag.php";
						}
						
						//$file = "/tag.php";
					}
					else
					{
						// do stuff
					}
				}
				else if($this->router->pageType() == "feed")
				{
					$this->pageData = $this->database->getPosts(0);
					$return = V_BASELOC . "/private/includes/";
					$file = "feed.php";
				}
				else
				{
					//echo $this->router->getPageOffset() . "<br>";
					//echo "boobs";
					$this->pageData = $this->database->getPage($this->router->fullURI());
					//print_r($this->pageData);
					// need some error checking for null pagedata
					if($this->pageData != null)
					{
						$this->arrayPosition = 0;
						$file = $this->arrayCustomFile("page");
					}
					else
					{
						// do stuff
					}
				}
			}
		}
		//print_r($this->pageData);
		
		if($file == null)
		{
			// we have some error which we need to figure out what to do.  for now we will just die
			//die("Invalid theme file");
			if($this->themeFileIsValid("404.php"))
			{
				$file = "/404.php";
			}
			else
			{
				$this->themeValidError = "Missing major components required for themes";
			}
		}
		// need to check for the theme file being valid here
		else
		{
			//echo substr($file, 1, strlen($file));
			if(!$this->themeFileIsValid(substr($file, 1, strlen($file))))
			{
				$this->themeValidError = "Theme file does not exist";
			}
		}
		
		
		// we can provbably streamline this
		$return .= $file;
		//$this->getCategoriesForPageData();
		return $return;
	}
	
	private function arrayCustomFile($defaultFile)
	{
		if($this->pageData[$this->arrayPosition]["themeFile"] == null)
		{
			$return = sprintf("/%s.php", $defaultFile);
		}
		else
		{
			$return = sprintf("/%s.php", $this->pageData[$this->arrayPosition]["themeFile"]);
		}
		
		return $return;
	}
	
	// dont use %CATEGORY% yet it doesnt work!
	private function checkUriPost()
	{
		$temp = explode("/", V_POSTFORMAT);
		
		$isBad = false;
		$return = true;
		
		if($this->router->uriLength() == count($temp))
		{
			$tmpCt = count($temp);
			
			for($i = 0; $i < $tmpCt; $i++)
			{
				switch((string)$temp[$i])
				{
					case "%MONTH%":
						if(intval($this->router->getUriPosition($i + 1)) > 12 || intval($this->router->getUriPosition($i + 1)) < 1)
						{
							$isBad = true;
						}
						break;
					case "%DAY%":
						if(intval($this->router->getUriPosition($i + 1)) > 31 || intval($this->router->getUriPosition($i + 1)) < 1)
						{
							$isBad = true;
						}
						break;
					case "%YEAR%":
						if(strlen($this->router->getUriPosition($i + 1)) < 4 || intval($this->router->getUriPosition($i + 1)) < 1000)
						{
							$isBad = true;
						}
						break;
					case "%TITLE%":
						if($this->router->getUriPosition($i + 1) == null)
						{
							$isBad = true;
						}
						break;
					/* case "%CATEGORY%":
						if(!$this->db->categoryCheck($this->router->getUriPosition($i + 1)))
						{
							$isBad = true;
						}
						break; */
					default:
						$isBad = true;
						break;
				}
				
				if($isBad)
					break;
			}
		}
		else
		{
			$return = false;
		}
		
		if($isBad)
		{
			$return = false;
		}
		return $return;
	}
	
	/**
	 * getPageData function.
	 * 
	 * @bried returns the array of page data.
	 * @access public
	 * @return Array of page data or null
	 */
	public function getPageData()
	{
		return $this->pageData;
	}
	
	/**
	 * postNext function.
	 * 
	 * @brief returns true if there are more posts to display, false if there are no new posts
	 * @brief if there is a next post it moves to that one so you can call the rest of the functions to get it's info
	 * @access public
	 * @return Boolean
	 */
	public function postNext()
	{
		$return = null;
		if($this->arrayPosition + 1 < count($this->pageData) && count($this->pageData) != 0)
		{
			$this->arrayPosition++;
			$return = true;
		}
		else
			$return = false;
			
		return $return;
	}
	
	/**
	 * getPostTitle function.
	 * 
	 * @brief returns the title of the current post.
	 * @access public
	 * @return Title of the current post
	 */
	public function getPostTitle()
	{
		$return = null;
		
		if(isset($this->pageData[$this->arrayPosition]["Title"]))
		{
			$return = $this->pageData[$this->arrayPosition]["Title"];
		}
		return $return;
	}
	
	/**
	 * getPostURI function.
	 * 
	 * @brief Returns only the URI of the post
	 * @access public
	 * @return String of URI or null
	 */
	public function getPostURI()
	{
		$return = null;
		
		if(isset($this->pageData[$this->arrayPosition]["URI"]))
		{
			$return = $this->pageData[$this->arrayPosition]["URI"];
		}
		return $return;
	}
	
	/**
	 * getPostURL function.
	 * 
	 * @brief returns the full URI of a post
	 * @access public
	 * @return String, URL of the post or null if no post
	 */
	public function getPostURL()
	{
		if(!V_HTACCESS)
		{
			$return = sprintf("%s%s%s%s", V_URL, V_HTTPBASE, "index.php", $this->pageData[$this->arrayPosition]["URI"]);
		}
		else
		{
			$tmp = V_HTTPBASE;
			$len = strlen($tmp);
			if($len > 0 && $tmp[$len-1] == "/")
			{
				$tmp = substr($tmp, 0, -1);
			}
			
			if(isset($this->pageData[$this->arrayPosition]["URI"]))
			{
				$return = sprintf("%s%s%s", V_URL, $tmp, $this->pageData[$this->arrayPosition]["URI"]);
			}
			else
			{
				$return = null;
			}
		}
		
		return $return;
	}
	
	/**
	 * getHtmlTitle function.
	 * 
	 * @brief makes a nice title that is to be used inside the <title> tags
	 * @access public
	 * @return String with the sites title
	 */
	public function getHtmlTitle()
	{
		// need to add logic to this so it can decide what title to return based on the page that is loaded
		$return = null;
		
		if(isset($this->pageData[$this->arrayPosition]["Title"]))
		{
			$return = sprintf('%s%s%s', $this->pageData[$this->arrayPosition]["Title"], " :: ", V_SITETITLE);
		}
		else
		{
			$return = V_SITETITLE;
		}
		
		return $return;
	}
	
	/**
	 * getPostBody function.
	 * 
	 * @brief returns just the body of a post with no formatting, so html won't act like html
	 * @access public
	 * @return String that contains the body of a post or null if doesn's exist
	 */
	public function getPostBody()
	{
		$return = null;
		
		if(isset($this->pageData[$this->arrayPosition]["PostData"]))
		{
			$return = stripslashes($this->pageData[$this->arrayPosition]["PostData"]);
		}
		return $return;
	}
	
	/**
	 * getPostBodyHTML function.
	 * 
	 * @brief Formatts the body so html runs and \n are converted to < br > "minus the spaces in there ofcourse"
	 * @access public
	 * @return String with the post body executing html on the client side, null if post doesn't exist
	 */
	public function getPostBodyHTML()
	{
		$return = null;
		
		if(isset($this->pageData[$this->arrayPosition]["PostData"]))
		{
			// now i could have put these into one big chain so,nl2br(html_entity_decode(stripslashes())) but i think this way is a bit easier to read.
			$return = $this->pageData[$this->arrayPosition]["PostData"];
			$return = stripslashes($return);
			$return = html_entity_decode($return);
			$return = nl2br($return);
		}
		return $return;
	}
	/**
	 * getPostAuthor function.
	 * 
	 * @brief Author who made the post
	 * @access public
	 * @return String containing the name of the author
	 */
	public function getPostAuthor()
	{
		$return = null;
		
		if(isset($this->pageData[$this->arrayPosition]["Author"]))
		{
			$return = $this->pageData[$this->arrayPosition]["Author"];
		}
		return $return;
	}
	
	/**
	 * getPostTime function.
	 * 
	 * @brief returns the post time based on the format that was passed into the function
	 * @brief to find out what formatting works look up the date function in the php documentation
	 * @access public
	 * @param mixed $format
	 * @return Formatted post time
	 */
	public function getPostTime($format)
	{
		$return = null;
		
		if($format != null && isset($this->pageData[$this->arrayPosition]["Date"]))
		{
			$strTime = strtotime($this->pageData[$this->arrayPosition]["Date"]);
			$return = date($format, $strTime);
		}
		return $return;
	}
	
	
	/**
	 * getPostTags function.
	 * 
	 * @brief Grabs the tags and puts then into a format like tag1, tag2, tag3
	 * @access public
	 * @return String containing the tags for the post
	 */
	public function getPostTags()
	{
		$return = null;
		if(isset($this->postTag[$this->pageData[$this->arrayPosition]["PrimaryKey"]]))
		{
			foreach($this->postTag[$this->pageData[$this->arrayPosition]["PrimaryKey"]] as $key)
			{
				$return .= $key["Name"] . ", ";
			}
		
			$return = substr($return, 0, -2);
		}
		return $return;
	}
	
	/**
	 * getPostTagsFormatted function.
	 * 
	 * @brief makes nice html links of the tags
	 * @access public
	 * @return String with tags with html tags for links
	 */
	public function getPostTagsFormatted()
	{
		$return = null;
		
		//print_r($this->postTag);
		if(isset($this->postTag[$this->pageData[$this->arrayPosition]["PrimaryKey"]]))
		{
			foreach($this->postTag[$this->pageData[$this->arrayPosition]["PrimaryKey"]] as $key)
			{
				$return .= '<a href="' . V_URL .V_HTTPBASE;
				if(!V_HTACCESS)
				{
					$return .= 'index.php/';
				}
			
				$return .= 'tag/' . $this->generateSubTagURI($key) . '">' . $key["Name"] . '</a>, ';
			}
			$return = substr($return, 0, -2);
		}
		return $return;
	}
	
	
	/**
	 * getPostCats function.
	 * 
	 * @brief Makes the categories for a post look nice, so something like cat1, cat2, cat3
	 * @access public
	 * @return String with the categories for a post
	 */
	public function getPostCats()
	{
		$return = null;
		
		if(isset($this->postCategory[$this->pageData[$this->arrayPosition]["PrimaryKey"]]))
		{
			foreach($this->postCategory[$this->pageData[$this->arrayPosition]["PrimaryKey"]] as $key)
			{
				$return .= $key["Name"] . ", ";
			}
			
			$return = substr($return, 0, -2);
		}
		return $return;
	}
	
	
	/**
	 * getPostCatsFormatted function.
	 * 
	 * @brief Returns a formatted string that contains the html for a category.  so for instance < href="http://someurl/category/cat1">cat1</a>
	 * @access public
	 * @return String that contains the html
	 */
	public function getPostCatsFormatted()
	{
		$return = null;
		if(isset($this->postCategory[$this->pageData[$this->arrayPosition]["PrimaryKey"]]))
		{
			foreach($this->postCategory[$this->pageData[$this->arrayPosition]["PrimaryKey"]] as $key)
			{
				//print_r($key);
				$tmp = V_URL . V_HTTPBASE;
				if(!V_HTACCESS)
				{
					$tmp .= 'index.php/';
				}
			
				$tmp .= 'category/' .  $this->generateSubCategoryURI($key);
			
				$return .= sprintf('<a href="%s">%s</a>, ', $tmp, $key["Name"]);
			}
			
			$return = substr($return, 0, -2);
		}
		return $return;
	}
	
	/**
	 * getPageTitle function.
	 * 
	 * @brief Gives you the title of the page if the page exists
	 * @access public
	 * @return String if the page has a title or null if there is nothing to do
	 */
	public function getPageTitle()
	{
		return $this->getPostTitle();
	}
	
	/**
	 * getPageURI function.
	 * 
	 * @brief Uri of the current page
	 * @access public
	 * @return String with the URI or null if something went wrong
	 */
	public function getPageURI()
	{
		return $this->getPostURI();
	}
	
	/**
	 * getPageURL function.
	 * 
	 * @brief URL of a page
	 * @access public
	 * @return String with the URL or null if there is no data
	 */
	public function getPageURL()
	{
		return $this->getPostURL();
	}
	
	/**
	 * getPageBody function.
	 * 
	 * @brief Gets the body of the page
	 * @access public
	 * @return String with the body of the page, null if there is nothing
	 */
	public function getPageBody()
	{
		$return = null;
		
		if(isset($this->pageData[$this->arrayPosition]["PageData"]))
		{
			$return = stripslashes($this->pageData[$this->arrayPosition]["PageData"]);
		}
		return $return;
	}
	
	/**
	 * getPageBodyHTML function.
	 * 
	 * @brief Page body but allowing html to be displayed
	 * @access public
	 * @return String with the body of the page, null if there is nothing
	 */
	public function getPageBodyHTML()
	{
		$return = null;
		
		if(isset($this->pageData[$this->arrayPosition]["PageData"]))
		{
			// now i could have put these into one big chain so,nl2br(html_entity_decode(stripslashes())) but i think this way is a bit easier to read.
			$return = $this->pageData[$this->arrayPosition]["PageData"];
			$return = stripslashes($return);
			$return = html_entity_decode($return);
			$return = nl2br($return);
		}
		return $return;
	}
	
	/*
	* we currently don't build the authors into the page
	public function getPageAuthor()
	{
		return $this->getPostAuthor();
	}
	*/
	
	/**
	 * getPageTime function.
	 * 
	 * @brief lets you format the output of the date
	 * @access public
	 * @param mixed $format
	 * @return String containing the date specified in format
	 */
	public function getPageTime($format)
	{
		return $this->getPostTime($format);
	}
	
	
	
	/**
	 * getCategories function.
	 * 
	 * @brief IT DOES NOTHING YET SO DON'T USE IT
	 * @access public
	 * @param mixed $number
	 * @return void
	 */
	public function getCategories($number)
	{
		$result = $this->db->getCategory();
	}
	
	
	private function generateUrlFromUri($URI)
	{
		// need to remove slashes from the begining of the URI if we are using htaccess
		$return = V_URL . V_HTTPBASE;
		
		if(V_HTACCESS)
		{
			$return .= "index.php";
		}
		else
		{
			if($return[strlen($return) - 1] == "/" && $URI[0] == "/")
			{
				$return = substr($return, 0, strlen($return) - 1);
			}
		}
		
		$return .= $URI;
		
		return $return;
	}
	
	// needs to check for null and no data in the array
	private function generateSubCategoryURI($array)
	{
		$subCat = $array["SubCat"];
		$URI = $array["URIName"];
		while($subCat > -1)
		{
			//echo $subCat;
			$data = $this->database->getCategoryOrTag($subCat, 0);
			//print_r($data);
			$temp = $data[0]["URIName"];
			$temp .= "/" . $URI;
			$URI = $temp;
			$subCat = $data[0]["SubCat"];
			//echo "woot";
		}
		
		return $URI;
	}
	
	private function generateSubTagURI($array)
	{
		$URI = null;
		if($array != null)
		{
			$subTag = $array["SubCat"];
			$URI = $array["URIName"];
			
			while($subTag > -1)
			{
				$data = $this->database->getCategoryOrTag($subTag, 1);
				$temp = $data[0]["URIName"];
				$temp .= "/" . $URI;
				$URI = $temp;
				$subTag = $data[0]["SubCat"];
			}
		}
		
		return $URI;
	}
	
	private function subCategoryURI()
	{
		
	}
	
	/**
	 * generateTags function.
	 * 
	 * @brief Makes the Tag Array.  Must be called prior to getting the tags for post(s) else you won't get anything from those functions.
	 * @access public
	 * @return void
	 */
	public function generateTags()
	{
		if($this->pageData != null && $this->postTag == null)
		{
			$tmpArr = array();
			
			foreach($this->pageData as $key)
			{
				array_push($tmpArr, $key["PrimaryKey"]);
			}
			
			//print_r($tmpArr);
			
			$this->postTag = $this->database->getPostCategoryOrTag($tmpArr, "tag");
		}
	}
	
	/**
	 * generateCategories function.
	 * 
	 * @brief Makes the Category array. Must be called prior to getting the categories for post(s) else you wont get anything from those functions.
	 * @access public
	 * @return void
	 */
	public function generateCategories()
	{
		if($this->pageData != null && $this->postCategory == null)
		{
			$tmpArr = array();
			
			foreach($this->pageData as $key)
			{
				array_push($tmpArr, $key["PrimaryKey"]);
			}
			
			$this->postCategory = $this->database->getPostCategoryOrTag($tmpArr, "category");
			
			//print_r($this->postCategory);
		}
	}
	
	/**
	 * getPostsIndex function.
	 * 
	 * @brief Sets up the pageData for posts on an index page since we don't automatically generate that for an index page.
	 * @access public
	 * @return void
	 */
	public function getPostsIndex()
	{
		$this->pageData = $this->database->getPosts(0);
	}
	
	/**
	 * getCorralArrayByName function.
	 * 
	 * @brief Returns and array with all the pages inside that corral
	 * @access public
	 * @param mixed $name
	 * @return Array with Title, URI, and URL of the pages inside this corral
	 */
	public function getCorralArrayByName($name)
	{
		$tmpArr = $this->database->getCorralByName($name);
		
		$count = count($tmpArr);
		
		for($i=0; $i < $count; $i++)
		{
			$tmpURL = sprintf("%s%s", V_URL, V_HTTPBASE);
			if(!V_HTACCESS)
			{
				$tmpURL .= "index.php";
			}
			$tmpURL .= sprintf("%s", $tmpArr[$i]["URI"]);
			$tmpArr[$i]["URL"] = $tmpURL;
		}
		
		return $tmpArr;
	}
	
	/**
	 * getFormattedCorralByName function.
	 * 
	 * @brief Takes the name of the corral and then if pages exist it makes a html list
	 * @access public
	 * @param mixed $name
	 * @return String containing an html list or null if no pages exist in that corral
	 */
	public function getFormattedCorralByName($name)
	{
		$tmpStr = null;
		$tmpArr = $this->getCorralArrayByName($name);
		
		//print_r($tmpArr);
		
		$count = count($tmpArr);
		
		for($i = 0; $i < $count; $i++)
		{
			$tmpStr .= sprintf('<li><a href="%s">%s</a>', $tmpArr[$i]["URL"], $tmpArr[$i]["Title"]);
		}
		
		return $tmpStr;
	}
	
	/**
	 * haveNextPostPage function.
	 * 
	 * @brief Lets us know if we have a next page we can go to
	 * @access public
	 * @return Boolean, True if we have a next page, false if we don't
	 */
	public function haveNextPostPage()
	{
		return $this->database->haveNextPage();
	}
	
	/**
	 * havePreviousPostPage function.
	 * 
	 * @brief Lets us know if we have a page previous to the one we are currently at
	 * @access public
	 * @return Boolean, True if we have a previous, false if we don't
	 */
	public function havePreviousPostPage()
	{
		$return = false;
		
		if(strtolower($this->router->pageType()) == "page" && $this->router->getPageOffset() > 0 && count($this->pageData) > 0)
		{
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * haveNextPostPageHTML function.
	 * 
	 * @brief Makes us a nice next link if we have a next page.  Can specify your own next text.
	 * @access public
	 * @param string $title. (default: "Next Page ->")
	 * @return String containing the link, null if we don't have a next page
	 */
	public function haveNextPostPageHTML($title = "Next Page ->")
	{
		$return = null;
		
		if($this->haveNextPostPage())
		{
			$offset = (int)$this->router->getPageOffset() + 2;
			
			if(!V_HTACCESS)
			{
				$tmpStr = sprintf("%s%s%s%d", V_URL, V_HTTPBASE, "index.php/page/", $offset);
			}
			else
			{
				$tmpStr = V_HTTPBASE;
				$len = strlen($tmpStr);
				if($len > 0 && $tmpStr[$len-1] == "/")
				{
					$tmpStr = substr($tmp, 0, -1);
				}
				
				$tmpStr = sprintf("%s%s%d", $tmpStr, "page/", $offset);
			}
			$return = sprintf('<a href="%s">%s</a>', $tmpStr, $title);
		}
		
		return $return;
	}
	
	/**
	 * havePreviousPostPageHTML function.
	 * 
	 * @brief Makes us a nice previous link if we have a previous page.  Can specify your own previous text
	 * @access public
	 * @param string $title. (default: "<- Previous Page")
	 * @return String containing the link, null if we don't have a previous page
	 */
	public function havePreviousPostPageHTML($title = "<- Previous Page")
	{
		$return = null;
		
		if($this->havePreviousPostPage())
		{
			$offset = (int)$this->router->getPageOffset();
			
			if(!V_HTACCESS)
			{
				$tmpStr = sprintf("%s%s%s%d", V_URL, V_HTTPBASE, "index.php/page/", $offset);
			}
			else
			{
				$tmpStr = V_HTTPBASE;
				$len = strlen($tmpStr);
				if($len > 0 && $tmpStr[$len-1] == "/")
				{
					$tmpStr = substr($tmp, 0, -1);
				}
				
				$tmpStr = sprintf("%s%s%d", $tmpStr, "page/", $offset);
			}
			$return = sprintf('<a href="%s">%s</a>', $tmpStr, $title);
		}
		
		return $return;
	}
	
	
	/**
	 * haveError function.
	 * 
	 * @brief if there was an error somewhere then we have an error
	 * @access public
	 * @return Boolean True if we have an error
	 */
	public function haveError()
	{
		$return = true;
		
		if($this->errorText == null)
		{
			$return = false;
		}
		
		return $return;
	}
	
	/**
	 * getError function.
	 * 
	 * @brief Error text
	 * @access public
	 * @return String with the error encountered or null if no error
	 */
	public function getError()
	{
		return $this->errorText;
	}
	
	/**
	 * themeError function.
	 * 
	 * @brief Do we have an error with themes?
	 * @access public
	 * @return Boolean True if we have an error with the themes
	 */
	public function themeError()
	{
		if($this->themeValidError == null)
		{
			$return = false;
		}
		else
		{
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * themeErrorText function.
	 * 
	 * @brief Error text related to theme errors
	 * @access public
	 * @return String with the error encountered or null if no error
	 */
	public function themeErrorText()
	{
		return $this->themeValidError;
	}
}
?>