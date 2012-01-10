<?php
/**
 * Does all the heavy lifting for themes
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * 
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
	private $pageDataCt = null;
	private $siteTitle = "";
	private $siteDescription = "";
	private $themeName = "";
	private $siteUrl = "";
	private $postFormat = "";
	private $postsPerPage = 0;
	private $baseLocation = "";
	private $templateData = null;
	private $currentUrl = null;
	private $siteBaseUrl = null;
	
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param GenericDatabase $database
	 * @param Router $router
	 * @param mixed $siteTitle
	 * @param mixed $siteDescription
	 * @param mixed $themeName
	 * @param mixed $siteUrl
	 * @param mixed $postsPerPage
	 * @param mixed $baseLocation
	 * @param TemplateData $templateData
	 * @return void
	 */
	public function __construct(GenericDatabase $database, Router $router, $siteTitle, $siteDescription, $themeName, SiteUrlGenerator $siteUrlGenerator, $postsPerPage, $baseLocation, TemplateData $templateData)
	{
		$this->database = $database;
		$this->router = $router;
		$this->siteTitle = $siteTitle;
		$this->siteDescription = $siteDescription;
		$this->themeName = $themeName;
		$this->postsPerPage = (int)$postsPerPage;
		$this->baseLocation = $baseLocation;
		$this->siteUrl = $siteUrlGenerator->generateSiteUrl();
		$this->templateData = $templateData;
		$this->currentUrl = $siteUrlGenerator->currentAddressWithoutPageUrl();
		$this->siteBaseUrl = $siteUrlGenerator->generateSiteBaseUrl();
	}
	
	
	/**
	 * processTemplateData function.
	 * 
	 * Grabs the already processed TemplateData and sets it up to be the pageData and gets a count of items in the array
	 * @access public
	 * @return void
	 */
	public function processTemplateData()
	{
		$this->pageData = $this->templateData->getData();
		$this->pageDataCt = count($this->pageData);
	}
	
	/**
	 * getPageData function.
	 * 
	 * Returns the array of page data.
	 * @access public
	 * @return array|null array of page data or null if nothing exists
	 */
	public function getPageData()
	{
		return $this->pageData;
	}
	
	/**
	 * haveNextPost function.
	 * 
	 * Returns true if there are more posts to display, false if there are no new posts
	 * if there is a next post it moves to that one so you can call the rest of the functions to get it's info
	 * @access public
	 * @return boolean True if there are more posts or false if there are none left
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
	 * Returns the title of the current post.
	 * @access public
	 * @return string|null string if the title exists or null if it doesn't
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
	 * Returns only the URI of the post
	 * @access public
	 * @return string|null string if the post has a uri or null if it doesn't
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
	 * Returns the full URI of a post
	 * @access public
	 * @return string|null URL of the post or null if no post
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
	 * Makes a nice title that is to be used inside the <title> tags
	 * @access public
	 * @return string|null string with the sites title or null if one doesn't exist
	 */
	public function getHtmlTitle()
	{
		// need to add logic to this so it can decide what title to return based on the page that is loaded
		$return = null;
		$type = $this->router->pageType();
		
		if($type === "page")
		{
			try
			{
				$return = "Page " . ($this->router->getPageOffset() + 1) . " :: " . $this->siteTitle;
			}
			catch(exception $e)
			{
				$return = "Error";
			}
		}
		else if($type === "tag" || $type === "category")
		{
			// searches the uri for the category or tag and then looks one place after that for the name of the category
			$return = $type . " " . $this->router->getUriPosition($this->router->searchURI($type) + 1);
			
			try
			{
				$page = $this->router->getPageOffset();
				
				if($page !== 0)
				{
					// incrementing the page by 1 because the getPageOffset returns us the offset, so page 2 would be page 1
					$return .= " Page " . ($page + 1);
				}
			}
			catch(exception $e)
			{
				// do nothing
			}
			
			$return .= " :: " . $this->siteTitle;
			
		}
		else if(isset($this->pageData[$this->arrayPosition]["Title"]))
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
	 * Returns just the body of a post with no formatting, so html won't act like html
	 * @access public
	 * @return string|null string that contains the body of a post or null if doesn's exist
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
	 * Formatts the body so html runs and \n are converted to < br > "minus the spaces in there ofcourse"
	 * @access public
	 * @return string|null string with the post body executing html on the client side, null if post doesn't exist
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
	 * Author who made the current post
	 * @access public
	 * @return string|null string containing the author of the current post null if it doesn't exist
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
	 * returns the post time based on the format that was passed into the function
	 * to find out what formatting works look up the date function in the php documentation
	 * @access public
	 * @param mixed $format
	 * @return string|null string containing the formatted post time or null if invalid
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
	 * Grabs the tags and puts then into a format like tag1, tag2, tag3
	 * @access public
	 * @return string|null string containing the tags for the post or null if none
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
	 * Makes nice html links of the tags
	 * @access public
	 * @return string|null string with html formatted tags. null if none exist
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
	 * Makes the categories for a post look nice, so something like cat1, cat2, cat3
	 * @access public
	 * @return string|null string with the categories for a post. null if none exist
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
	 * Makes nice html formatted categories
	 * @access public
	 * @return string|null string that contains the html formatted categories. null if none exist
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
	 * Gives you the title of the page if the page exists
	 * @access public
	 * @return string|null string if the page has a title or null if there is nothing to do
	 */
	public function getPageTitle()
	{
		return $this->getPostTitle();
	}
	
	/**
	 * getPageUri function.
	 * 
	 * Uri of the current page
	 * @access public
	 * @return string string with the URI or null if something went wrong
	 */
	public function getPageUri()
	{
		return $this->getPostUri();
	}
	
	/**
	 * getPageURL function.
	 * 
	 * URL of a page
	 * @access public
	 * @return string|null string with the URL or null if there is no data
	 */
	public function getPageUrl()
	{
		return $this->getPostUrl();
	}
	
	/**
	 * getPageBody function.
	 * 
	 * Gets the body of the page
	 * @access public
	 * @return string|null string with the body of the page, null if there is nothing
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
	 * Page body but allowing html to be displayed
	 * @access public
	 * @return string|null string with the body of the page, null if there is nothing
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
	
	/**
	 * getPageTime function.
	 * 
	 * Lets you format the output of the date
	 * @access public
	 * @param mixed $format
	 * @return string|null string containing the date specified in format. null if invalid
	 */
	public function getPageTime($format)
	{
		return $this->getPostTime($format);
	}
	
	
	
	/**
	 * getCategories function.
	 * 
	 * This is probably going to be marked for removal. This doesn't work anyway
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
	
	/**
	 * generateTags function.
	 * 
	 * Makes the Tag Array.  Must be called prior to getting the tags for post(s) else you won't get anything from those functions.
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
	 * Makes the Category array. Must be called prior to getting the categories for post(s) else you wont get anything from those functions.
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
	 * Sets up the pageData for posts on an index page since we don't automatically generate that for an index page. (also used for feeds so they have data to generate into a feed)
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
	 * Returns an array with all the pages inside that corral
	 * @access public
	 * @param mixed $name
	 * @return array|null array with Title, URI, and URL of the pages inside this corral. null if it doesn't exist
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
	 * Takes the name of the corral and then if pages exist it makes a html list
	 * @access public
	 * @param mixed $name
	 * @return string|null string containing an html list or null if no pages exist in that corral
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
	 * Lets us know if we have a next page we can go to
	 * @access public
	 * @return boolean True if we have a next page, false if we don't
	 */
	public function haveNextPostPage()
	{
		return $this->database->haveNextPage();
	}
	
	/**
	 * havePreviousPostPage function.
	 * 
	 * Lets us know if we have a page previous to the one we are currently at
	 * @access public
	 * @return boolean True if we have a previous, false if we don't
	 */
	public function havePreviousPostPage()
	{
		$return = false;
		
		// by removing strtolower($this->router->pageType()) === "page" we should be able to show previous post pages on all pages
		if($this->router->getPageOffset() > 0 && $this->pageDataCt > 0)
		{
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * haveNextPostPageHtml function.
	 * 
	 * Makes us a nice next link if we have a next page.  Can specify your own next text.
	 * @access public
	 * @param string $title. (default: "Next Page ->")
	 * @return string|null containing the link, null if we don't have a next page
	 */
	public function haveNextPostPageHtml($title = "Next Page ->")
	{
		$return = null;
		
		if($this->haveNextPostPage())
		{
			$offset = (int)$this->router->getPageOffset() + 2;
			
			$return = sprintf('<a href="%spage/%d">%s</a>', $this->currentUrl, $offset, $title);
		}
		
		return $return;
	}
	
	/**
	 * havePreviousPostPageHtml function.
	 * 
	 * Makes us a nice previous link if we have a previous page.  Can specify your own previous text
	 * indexBase allows us to set if the first page should have /page/1 appended to it or not
	 * @access public
	 * @param string $title. (default: "<- Previous Page")
	 * @param boolean $indexBase. (default: false)
	 * @return string|null containing the link, null if we don't have a previous page
	 */
	public function havePreviousPostPageHtml($title = "<- Previous Page", $indexBase = false)
	{
		$return = null;
		
		if($this->havePreviousPostPage())
		{
			$offset = $this->router->getPageOffset();
			
			if($indexBase === true && $offset === 1)
			{
				$return = sprintf('<a href="%s">%s</a>', $this->currentUrl, $title);
			}
			else
			{
				$return = sprintf('<a href="%spage/%d">%s</a>', $this->currentUrl, $offset, $title);
			}
		}
		
		return $return;
	}
	
	/**
	 * getSnippetByName function.
	 * 
	 * Gets the snippet data from the snippet specified by the name
	 * @access public
	 * @param mixed $name
	 * @return string|null string containing the snippet with html escaped. null if it doesn't exist
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
	 * Gets the snippet data from the snippet specified by the name and allows html to be run by the browser
	 * @access public
	 * @param mixed $name
	 * @return string|null string containing the snippet data with HTML. null if it doesn't exist
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
	 * Builds the http location of a theme
	 * @access public
	 * @return string containing the http address of the base of a theme
	 */
	public function themeBaseLocation()
	{
		static $return = null;
		
		if($return === null)
		{
			$return = $this->siteBaseUrl . '/themes/' . $this->themeName . '/'; 
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
	
	// when we clean the feed part up we should keep this
	public function getSiteDescription()
	{
		return $this->siteDescription;
	}
	
}
?>