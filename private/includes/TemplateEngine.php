<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Does all the heavy lifting for themes
 * 
 */
class TemplateEngine
{

	private $database = null;
	private $router = null;
	private $pageData = null;
	private $postCategory = null;
	private $postTag = null;
	private $arrayPosition = 0;
	private $errorText = null;
	private $pageDataCt = null;
	// feeds are handled by the template engine.  Could possibly seperate it out later to be its own thing
	private $feedAuthor = "";
	private $feedAuthorEmail = "";
	private $feedPubSubHubBub = "";
	private $feedPubSubHubBubSubscribe = "";
	private $siteTitle = "";
	private $siteDescription = "";
	private $themeName = "";
	private $siteUrl = "";
	private $postFormat = "";
	private $postsPerPage = 0;
	private $baseLocation = "";
	private $templateData = null;
	
	
	/**
	 * __construct function.
	 * 
	 * @brief you need at least a 404.php for the theme to be valid
	 * @access public
	 */
	public function __construct($database, $router, $siteTitle, $siteDescription, $themeName, $siteUrl, $postFormat, $postsPerPage, $baseLocation, $templateData)
	{
		$this->database = $database;
		$this->router = $router;
		$this->siteTitle = $siteTitle;
		$this->siteDescription = $siteDescription;
		$this->themeName = $themeName;
		$this->postFormat = $postFormat;
		$this->postsPerPage = (int)$postsPerPage;
		$this->baseLocation = $baseLocation;
		$this->siteUrl = $siteUrl;
		$this->templateData = $templateData;
		$this->pageData = $this->templateData->getData();
		//print_r($this->pageData);
		$this->pageDataCt = count($this->pageData);
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
	 * haveNextPost function.
	 * 
	 * @brief returns true if there are more posts to display, false if there are no new posts
	 * @brief if there is a next post it moves to that one so you can call the rest of the functions to get it's info
	 * @access public
	 * @return Boolean
	 */
	public function haveNextPost()
	{
		$return = null;
		// since we are starting at 0 so everything else works we need this to start at -1 so that we don't miss the first page when we are in a loop
		// this allows us to not have to call other functions when we are doing a page/single post
		// because haveNextPost is called before the first post we need to make this one less than it is since we are going to increment it anyway
		// the static value remembers what it was set at the previous run
		static $runOnce = false;
		
		if(!$runOnce)
		{
			$this->arrayPosition--;
		}
		
		
		if($this->arrayPosition + 1 < $this->pageDataCt && $this->pageDataCt != 0)
		{
			$this->arrayPosition++;
			$return = true;
			$runOnce = true;
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
	 * getPostUri function.
	 * 
	 * @brief Returns only the URI of the post
	 * @access public
	 * @return String of URI or null
	 */
	public function getPostUri()
	{
		$return = null;
		
		if(isset($this->pageData[$this->arrayPosition]["URI"]))
		{
			$return = $this->pageData[$this->arrayPosition]["URI"];
		}
		return $return;
	}
	
	/**
	 * getPostUrl function.
	 * 
	 * @brief returns the full URI of a post
	 * @access public
	 * @return String, URL of the post or null if no post
	 */
	public function getPostUrl()
	{

		if(isset($this->pageData[$this->arrayPosition]["URI"]))
		{
			$return = $this->siteUrl . $this->pageData[$this->arrayPosition]["URI"];
		}
		else
		{
			$return = null;
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
			$return = $this->pageData[$this->arrayPosition]["Title"] . " :: " . $this->siteTitle;
		}
		else
		{
			$return = $this->siteTitle;
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
	 * getPostBodyHtml function.
	 * 
	 * @brief Formatts the body so html runs and \n are converted to < br > "minus the spaces in there ofcourse"
	 * @access public
	 * @return String with the post body executing html on the client side, null if post doesn't exist
	 */
	public function getPostBodyHtml()
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
			$return = date($format, strtotime($this->pageData[$this->arrayPosition]["Date"]));
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
				// for some reason I feel like its good to set a null to a string before I use it
				if($return === null)
				{
					$return = "";
				}
				
				// might want to make sure $key["Name"] is valid before using it.
				$return .= sprintf('<a href="%s/tag/%s">%s</a>, ', $this->siteUrl, $this->generateSubTagUri($key), $key["Name"]);
			}
			$return = substr($return, 0, -2);
		}
		return $return;
	}
	
	
	/**
	 * getPostCategories function.
	 * 
	 * @brief Makes the categories for a post look nice, so something like cat1, cat2, cat3
	 * @access public
	 * @return String with the categories for a post
	 */
	public function getPostCategories()
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
	 * getPostCategoriesFormatted function.
	 * 
	 * @brief Returns a formatted string that contains the html for a category.  so for instance < href="http://someurl/category/cat1">cat1</a>
	 * @access public
	 * @return String that contains the html
	 */
	public function getPostCategoriesFormatted()
	{
		$return = null;
		if(isset($this->postCategory[$this->pageData[$this->arrayPosition]["PrimaryKey"]]))
		{
			foreach($this->postCategory[$this->pageData[$this->arrayPosition]["PrimaryKey"]] as $key)
			{
				// feels like a good idea to make the null a string before I use it
				if($return === null)
				{
					$return = "";
				}
				
				// might want to make sure $key["Name"] is valid before using it.
				$return .= sprintf('<a href="%s/category/%s">%s</a>, ', $this->siteUrl, $this->generateSubCategoryUri($key), $key["Name"]);
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
	 * getPageUri function.
	 * 
	 * @brief Uri of the current page
	 * @access public
	 * @return String with the URI or null if something went wrong
	 */
	public function getPageUri()
	{
		return $this->getPostUri();
	}
	
	/**
	 * getPageURL function.
	 * 
	 * @brief URL of a page
	 * @access public
	 * @return String with the URL or null if there is no data
	 */
	public function getPageUrl()
	{
		return $this->getPostUrl();
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
	public function getPageBodyHtml()
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
	
	
	private function generateUrlFromUri($uri)
	{
		$return = $this->siteUrl;
		
		// might be able to switch this to if(isset($uri[0]) && $uri[0] ==== '/')
		$uriLen = strlen($uri);
		if($uriLen > 0 && $uri[0] === '/')
		{
			$uri = substr($uri, 1);
		}
		
		$return .= $uri;
		
		return $return;
	}
	
	// needs to check for null and no data in the array
	private function generateSubCategoryUri($array)
	{
		$subCat = $array["SubCat"];
		$URI = $array["URIName"];
		
		while($subCat > -1)
		{
			$data = $this->database->getCategoryOrTag($subCat, 0);
			$URI = $data[0]["URIName"] . "/" . $URI;
			$subCat = $data[0]["SubCat"];
		}
		
		return $URI;
	}
	
	private function generateSubTagUri($array)
	{
		$URI = null;
		if($array != null)
		{
			$subTag = $array["SubCat"];
			$URI = $array["URIName"];
			
			while($subTag > -1)
			{
				$data = $this->database->getCategoryOrTag($subTag, 1);
				$URI = $data[0]["URIName"] . "/" . $URI;
				$subTag = $data[0]["SubCat"];
			}
		}
		
		return $URI;
	}
	
	private function subCategoryUri()
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
		if($this->pageData != null && $this->postTag === null)
		{
			$tmpArr = array();
			
			foreach($this->pageData as $key)
			{
				$tmpArr[] = $key["PrimaryKey"];
			}
			
			$this->postTag = $this->database->getPostCategoryOrTag($tmpArr, 1);
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
		if($this->pageData != null && $this->postCategory === null)
		{
			$tmpArr = array();
			
			foreach($this->pageData as $key)
			{
				$tmpArr[] = $key["PrimaryKey"];
			}
			
			$this->postCategory = $this->database->getPostCategoryOrTag($tmpArr, "category");
			
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
		$this->pageData = $this->database->getPosts($this->postsPerPage, 0);
		
		if(!empty($this->pageData))
		{
			$this->pageDataCt = count($this->pageData);
		}
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
		
		for($i = 0; $i < $count; $i++)
		{	
			$tmpArr[$i]["URL"] = $this->siteUrl . $tmpArr[$i]["URI"];
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
		
		if(strtolower($this->router->pageType()) === "page" && $this->router->getPageOffset() > 0 && $this->pageDataCt > 0)
		{
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * haveNextPostPageHtml function.
	 * 
	 * @brief Makes us a nice next link if we have a next page.  Can specify your own next text.
	 * @access public
	 * @param string $title. (default: "Next Page ->")
	 * @return String containing the link, null if we don't have a next page
	 */
	public function haveNextPostPageHtml($title = "Next Page ->")
	{
		$return = null;
		
		if($this->haveNextPostPage())
		{
			$offset = (int)$this->router->getPageOffset() + 2;
			
			$return = sprintf('<a href="%s/page/%d">%s</a>', $this->siteUrl, $offset, $title);
		}
		
		return $return;
	}
	
	/**
	 * havePreviousPostPageHtml function.
	 * 
	 * @brief Makes us a nice previous link if we have a previous page.  Can specify your own previous text
	 * @access public
	 * @param string $title. (default: "<- Previous Page")
	 * @return String containing the link, null if we don't have a previous page
	 */
	public function havePreviousPostPageHtml($title = "<- Previous Page")
	{
		$return = null;
		
		if($this->havePreviousPostPage())
		{
			$offset = $this->router->getPageOffset();
			
			if($offset === 1)
			{
				$return = sprintf('<a href="%s">%s</a>', $this->siteUrl, $title);
			}
			else
			{
				$return = sprintf('<a href="%s/page/%d">%s</a>', $this->siteUrl, $offset, $title);
			}
		}
		
		return $return;
	}
	
	/**
	 * getSnippetByName function.
	 * 
	 * @brief gets the snippet data from the snippet specified by the name
	 * @access public
	 * @param mixed $name
	 * @return String containing the snippet with html escaped
	 */
	public function getSnippetByName($name)
	{
		$return = null;
		
		$tmpArr = $this->database->getSnippetByName($name);
		
		//print_r($tmpArr);
		
		if(isset($tmpArr["SnippetData"]))
		{
			$return = stripslashes($tmpArr["SnippetData"]);
		}
		
		return $return;
	}
	
	/**
	 * getSnippetByNameHtml function.
	 * 
	 * @brief gets the snippet data from the snippet specified by the name and allows html to be run by the browser
	 * @access public
	 * @param mixed $name
	 * @return String containing the snippet data with HTML
	 */
	public function getSnippetByNameHtml($name, $lineBreaks = true)
	{
		$return = null;
		
		$tmpArr = $this->database->getSnippetByName($name);
		
		if(isset($tmpArr["SnippetData"]))
		{
			$return = $tmpArr["SnippetData"];
			$return = stripslashes($return);
			$return = html_entity_decode($return);
			if($lineBreaks)
			{
				$return = nl2br($return);
			}
		}
		
		return $return;
	}
	
	/**
	 * themeBaseLocation function.
	 * 
	 * @brief Builds the http location of a theme
	 * @access public
	 * @return String containing the http address of the base of a theme
	 */
	public function themeBaseLocation()
	{
		static $return = null;
		
		if($return === null)
		{
			$return = $this->siteUrl . '/themes/' . $this->themeName . '/'; 
		}
		
		return $return;
	}
	
	public function getFeedType()
	{
		// default feed type
		$return = null;
		$type = $this->router->getUriPosition(2);
		
		if($this->router->uriLength() <= 2)
		{
			if($type === null || $type === "rss")
			{
				$return = "rss";
			}
			else if($type === "atom")
			{
				$return = "atom";
			}
			else
			{
				$this->errorText = "Invalid Feed Type";
			}
		}
		else
		{
			$this->errorText = "Invalid Feed Address";
		}
		
		return $return;
	}
	
	public function lastUpdatedTime($format)
	{
		$return = null;
		
		if(isset($this->pageData[0]["Date"]))
		{
			$tmp = $this->arrayPosition;
			$this->arrayPosition = 0;
			$return = $this->getPostTime($format);
			$this->arrayPosition = $tmp;
		}
		
		return $return;
	}
	
	public function siteUrl($uri = null)
	{
		$return = null;
		
		if($uri === null)
		{
			$return = $this->siteUrl;
		}
		else
		{
			$return = $this->generateUrlFromUri($uri);
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
		
		if($this->errorText === null)
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
	
	// when we clean the feed part up we should keep this
	public function getSiteDescription()
	{
		return $this->siteDescription;
	}
	
}
?>