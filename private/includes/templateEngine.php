<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Does all the heavy lifting for themes
 * 
 */
class templateEngine
{

	private $database = null
	private $router = null;
	private $pageData = null;
	private $pageCategory = null;
	private $pageTag = null;
	private $arrayPosition = -1;
	private $errorText = null;
	
	public function __construct($database, $router)
	{
		$this->database = $database;
		$this->router = $router;
		
		// we need to check if at least an index.php exists for the theme
		if(!$this->themeFileIsValid("index.php"))
		{
			die("no valid theme file found. You have no index.php");
		}
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
		
		if(strtolower($this->router->pageType()) == "" || strtolower($this->router->pageType()) == "page")
		{
			$this->pageData = $this->database->getPosts($this->router->getPageOffset());
			//print_r($this->database->getPostCategory(2));
			// this is just here to debug a function
			/*
			$temp1234 = $this->database->getPostCategory(2);
			foreach($temp1234 as $key)
			{
				//print_r($key);
				echo $this->generateSubCategoryURI($key);
			}
			*/
			
			// need to move this so it gets called all the time
			$this->getCategoriesForPageData();
			
			$file = "/index.php";
		}
		else
		{
			$temp = explode("/", V_POSTFORMAT);
			echo $this->router->uriLength() . "<br>";
			echo count($temp). "<br>";
			if($this->checkUriPost())
			{
				echo "blah";
				$this->pageData = $this->database->getSinglePost($this->router->fullURI());
				if($this->pageData != null)
				{
					// need to make sure this theme file is legit
					$file = "/" . $this->pageData[0]["themeFile"];
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
						echo "true";
						$this->pageData = $this->database->getPostsInCategoryOrTag($this->router->getUriPosition($this->router->uriLength()), 0);
						print_r($this->pageData);
					}
					else if($this->router->uriLength() == 1)
					{
						//echo "cool";
						$this->pageData = $this->database->listCategoriesOrTags(0);
					}
					else
					{
						echo "false";
						
					}		
					//$this->pageData = $this->database->getSpecificCategory($this->router->getUriPosition(2), $this->router->getPageOffset() * 10);
					// need some error checking for null pagedata
					if($this->pageData != null)
					{
						// just here for default
						$file = "/index.php";
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
						echo "true";
						$this->pageData = $this->database->getPostsInCategoryOrTag($this->router->getUriPosition($this->router->uriLength()), 1);
						print_r($this->pageData);
					}
					else
					{
						echo "false";
						
					}
					// need some error checking for null pagedata
					if($this->pageData == null)
					{
						// just here for default
						$file = "/index.php";
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
					echo "boobs";
					$this->pageData = $this->database->getPage($this->router->fullURI());
					//print_r($this->pageData);
					// need some error checking for null pagedata
					if($this->pageData != null)
					{
						// need to make sure this theme file is legit
						$file = "/" . $this->pageData[0]["themeFile"];
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
			$file = "/404.php";
		}
		// we can provbably streamline this
		$return .= $file;
		$this->getCategoriesForPageData();
		$this->getTagsForPageData();
		return $return;
	}
	
	// dont use %CATEGORY% yet it doesnt work! its just drycoded
	private function checkUriPost()
	{
		$temp = explode("/", V_POSTFORMAT);
		
		$isBad = false;
		$return = true;
		
		if($this->router->uriLength() == count($temp))
		{
			for($i = 0; $i < count($temp); $i++)
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
						if(intval($this->router->getUriPosition($i + 1)) > 31 || intval($this->router->getUriPosition($i + 1)) < 11)
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
					case "%CATEGORY%":
						if(!$this->db->categoryCheck($this->router->getUriPosition($i + 1)))
						{
							$isBad = true;
						}
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
	 * @access public
	 * @return pageData
	 */
	public function getPageData()
	{
		return $this->pageData;
	}
	
	/**
	 * postNext function.
	 * 
	 * @access public
	 * @return True (if we have another post) or False (if we don't)
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
	 * @access public
	 * @return Title of the current post
	 */
	public function getPostTitle()
	{
		return $this->pageData[$this->arrayPosition]["Title"];
	}
	
	/**
	 * getPostURI function.
	 * 
	 * @access public
	 * @return URI of the current post
	 */
	public function getPostURI()
	{
		return $this->pageData[$this->arrayPosition]["URI"];
	}
	
	/**
	 * getPostBody function.
	 * 
	 * @access public
	 * @return The body of the current post
	 */
	public function getPostBody()
	{
		return $this->pageData[$this->arrayPosition]["PostData"];
	}
	
	/**
	 * getPostAuthor function.
	 * 
	 * @access public
	 * @return Author of the current post
	 */
	public function getPostAuthor()
	{
		return $this->pageData[$this->arrayPosition]["Author"];
	}
	
	// this needs to be a bit more intricate and format the time and whatnot, but its just simple for now so we can get some test data
	public function getPostTime($format)
	{
		$strTime = strtotime($this->pageData[$this->arrayPosition]["Date"]);
		return date($format, $strTime);
	}
	
	// returns the tags for a post unformatted
	public function getPostTags()
	{
		return $this->pageData[$this->arrayPosition]["Tags"];
	}
	// generates a formatted string with the tags properly formated in links that take them to /tags/tagname
	public function getPostTagsFormatted()
	{
		$return = null;
		/*
		$array = explode(", ", $this->pageData[$this->arrayPosition]["Tags"]);
		foreach($array as $key)
		{
			if($key != null)
			{
				if(substr($key, (strlen($key) -1)) == ",")
					$key = substr($key, 0, -1);
				$return .= '<a href="' . V_URL . V_HTTPBASE;
				if(!V_HTACCESS)
				{
					$return .= "index.php/";
				}
				$return .= 'tag/' . $key . '">' . $key . '</a>, ';
			}
		}
		$return = substr($return, 0, -2);
		
		*/
		
		foreach($this->pageTag[$this->arrayPosition] as $key)
		{
			$return .= '<a href="' . V_URL .V_HTTPBASE;
			if(!V_HTACCESS)
			{
				$return .= 'index.php/';
			}
			
			$return .= 'tag/' . $this->generateSubTagURI($key) . '">' . $key["Name"] . '</a>';
		}
		
		return $return;
	}
	
	// returns the cats for a post unformatted
	public function getPostCats()
	{
		return $this->pageData[$this->arrayPosition]["Category"];
	}
	
	// generates a formatted string with the categories properly formatted in links that take them to /categories/category
	public function getPostCatsFormatted()
	{
		$return = null;
		/*
		$array = explode(", ", $this->pageData[$this->arrayPosition]["Category"]);
		foreach($array as $key)
		{
			if($key != null)
			{
				if(substr($key, (strlen($key)-1)) == ",")
					$key = substr($key, 0, -1);
				$return .= '<a href="' . V_URL . V_HTTPBASE;
				if(!V_HTACCESS)
				{
					$return .= "index.php/";
				}
				$return .= 'category/' . $key . '">' . $key . '</a>, ';
			}
		}
		$return = substr($return, 0, -2);
		*/
		//echo count($this->pageCategory[$this->arrayPosition]);
		//print_r($this->pageCategory[$this->arrayPosition]);
		foreach($this->pageCategory[$this->arrayPosition] as $key)
		{
			//print_r($key);
			$tmp = V_URL . V_HTTPBASE;
			if(!V_HTACCESS)
			{
				$tmp .= 'index.php/';
			}
			
			$tmp .= 'category/' .  $this->generateSubCategoryURI($key);
			
			$return .= sprintf('<a href="%s">%s</a>', $tmp, $key["Name"]);
		}
		
		// need to test this out after pretty much rewriting the previous lines
		$return = substr($return, 0, -2);
		
		
		
		
		
		return $return;
	}
	
	public function getCategories($number)
	{
		$result = $this->db->getCategory();
	}
	
	public function pageCorralName($id)
	{
		$id = intval($id);
		
		$pages = $this->db->corralPage($id);
		$return = null;
		
		foreach($pages as $key)
		{
			$return .= sprintf('<a href="%s">%s</a>', generateUrlFromUri($key["URI"]), $key["Title"]);
		}
		
		return $return;
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
			if($return[strlen($return) - 1] == "/" and $URI[0] == "/")
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
		while($subCat != 0)
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
			
			while($subTag != 0)
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
	
	private function getCategoriesForPageData()
	{
		
		
		if($this->pageData != null)
		{
			$temp = array();
			
			foreach($this->pageData as $key)
			{
				array_push($temp, $this->database->getPostCategoryOrTag($key["PrimaryKey"], 0));
			}
			$this->pageCategory = $temp;
		}
		else
		{
			$this->pageCategory = null;
		}
		
		//print_r($temp);
		
	}
	
	private function getTagsForPageData()
	{
		if($this->pageData != null)
		{
			$temp = array();
			
			foreach($this->pageData as $key)
			{
				array_push($temp, $this->database->getPostCategoryOrTag($key["PrimaryKey"], 1));
			}
			$this->pageTag = $temp;
		}
		else
		{
			$this->pageTag = null;
		}
	}
	
	/**
	 * haveError function.
	 * 
	 * @access public
	 * @return Boolean
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
	 * @access public
	 * @return String with the error encountered
	 */
	public function getError()
	{
		return $this->errorText;
	}
}
?>