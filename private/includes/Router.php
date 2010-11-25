<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Handles simple routing of pages
 *
 */
class Router
{
	protected $uri = null;
	protected $firstPart = null;
	protected $uriArray = null;
	protected $htaccess = null;
	protected $requestMethod = null;
	protected $base = null;
	protected $uriError = false;
	
	
	/**
	 * __construct function.
	 * 
	 * @brief Sets everything up
	 * @access public
	 * @param mixed $htaccess
	 * @param mixed $base (default = V_HTTPBASE)
	 * @return void
	 */
	public function __construct($htaccess, $base = V_HTTPBASE)
	{
		$this->htaccess = $htaccess;
		$this->uri = $_SERVER['REQUEST_URI'];
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
		$this->base = $base;
		$this->buildRouting();
	}
	
	
	/**
	 * buildRouting function.
	 * 
	 * @brief Makes cool things happen.
	 * @access private
	 * @return void
	 */
	private function buildRouting()
	{
		// figure out what happens when using htaccess
		// if its null we are going to assume its /
		if($this->base != "/" && $this->base != null)
		{
			$temp = explode($this->base, $this->uri);
			$this->uri = $temp[1];
		}
		// this else statement is here to strip off the first / as it will explode to nothing in an array
		// we end up having the database put the / back on when it is needed in things like URI
		else
		{
			$this->uri = substr($this->uri, 1, strlen($this->uri));
		}
		
		$this->uriArray = explode("/", $this->uri);
		
		$tmpCt = count($this->uriArray);
		
		// removes the end if there is a trailing slash and we made that position empty
		if(empty($this->uriArray[$tmpCt-1]))
		{
			unset($this->uriArray[$tmpCt-1]);
		}
		
		// could simplify the else statement a bit
		// figure out what actually happens when we use htaccess
		if($this->htaccess)
		{
			if(isset($this->uriArray[0]))
			{
				$this->firstPart = $this->uriArray[0];
			}
			else
			{
				$this->firstPart = "";
			}
		}
		else
		{
			$temp = explode("index.php", $this->uri);
			
			if(count($this->uriArray) > 1)
			{
				$this->firstPart = $this->uriArray[1];
				$this->uri = $temp[1];
				
				// this removes the trailing slash if one exists
				if(substr($this->uri, (strlen($this->uri) - 1)) == '/')
				{
					$this->uri = substr($this->uri, 0, -1);
				}
				
				// this removes the starting slash if it exists. useful because we are now exploding the string around index.php
				// needed because when we look stuff up in the db we add the starting slash
				if(substr($this->uri, 0, 1) == '/')
				{
					$this->uri = substr($this->uri, 1);
				}
				
				$this->uriArray = explode("/", $this->uri);
			}
			else
			{
				$this->uri = "";
				$this->firstPart = "";
			}
		
		}
		
		//print_r($this->uriArray);
	}
	
	/**
	 * pageType function.
	 * 
	 * @brief Returns the first part of the uri. /test/1234/hello would return test. / returns "" (an empty string).
	 * @access public
	 * @return String
	 */
	public function pageType()
	{
		return $this->firstPart;
	}
	
	/**
	 * fullURI function.
	 * 
	 * @brief Returns the uri as a string.
	 * @access public
	 * @return String
	 */
	public function fullURI()
	{
		return $this->uri;
	}
	
	public function fullURIRemoveTrailingSlash()
	{
		$return = $this->uri;
		
		if(substr($return, -1) == "/")
		{
			$return = substr($return, 0, -1);
		}
		
		return $return;
	}
	

	/**
	 * getUriPosition function.
	 * 
	 * @brief returns the value of the uri at the sepcific position
	 * @access public
	 * @param mixed $position
	 * @return If the position exists then it returns that value, else it returns null
	 */
	public function getUriPosition($position)
	{
		$return = null;
		if(count($this->uriArray) >= $position && $position > 0)
		{
			$position = $position - 1;
			//echo $position;
			$return = $this->uriArray[$position];
			//print_r($this->uriArray[$position]);
		}
		
		return $return;
	}
	
	/**
	 * requestMethod function.
	 * 
	 * @brief returns the type of the request, GET/POST
	 * @access public
	 * @return String
	 */
	public function requestMethod()
	{
		return $this->requestMethod;
	}
	
	/**
	 * uriLength function.
	 * 
	 * @brief Returns the number of items in the uri.  /test/123/afs/ would return 3.
	 * @access public
	 * @return Integer
	 */
	public function uriLength()
	{
		return count($this->uriArray);
	}
	
	/**
	 * searchURI function.
	 * 
	 * @brief Allows you to search the URI to find something. if the uri was /test123/hello/sup and you searched for hello you get 2
	 * @access public
	 * @param mixed $find
	 * @return Integer that is the position of the search in the URI, -1 if it doesn't exist
	 */
	public function searchURI($find)
	{
		$return = -1;
		
		$count = count($this->uriArray);
		
		for($i = 0; $i < $count; $i++)
		{
			if($this->uriArray[$i] == $find)
			{
				$return = $i + 1;
				break;
			}
		}
		
		return $return;
	}
	
	public function evenURIParts()
	{
		$count = count($this->uriArray);
		
		if($count & 1)
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
	 * getPageOffset function.
	 * 
	 * @brief Figures out what page we are on from the URI and returns what it figures out
	 * @access public
	 * @return Integer
	 */
	public function getPageOffset()
	{
		$page = 0;
		$position = null;
		$found = false;
		// could toss in a break if we want it to stop after finding the first "page"
		foreach($this->uriArray as $key)
		{
			if($found)
			{
				$page = intval($key);
				$found = false;
			}
			
			if(strtolower($key) == "page")
			{
				$found = true;
			}
		}
		
		if($page > 0)
		{
			$page = $page - 1;
		}
		
		return $page;
	}
}

?>