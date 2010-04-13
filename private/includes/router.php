<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Handles simple routing of pages
 *
 */
class router
{
	protected $uri = null;
	protected $firstPart = null;
	protected $uriArray = null;
	protected $htaccess = null;
	protected $requestMethod = null;
	protected $base = null;
	
	
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
		
		// could simplify the else statement a bit
		// figure out what actually happens when we use htaccess
		if($this->htaccess)
		{
			$this->firstPart = $this->uriArray[0];
		}
		else
		{
			$temp = explode("index.php/", $this->uri);
			if(count($this->uriArray) > 1)
			{
				$this->firstPart = $this->uriArray[1];
				$this->uri = $temp[1];
				
				if(substr($this->uri, (strlen($this->uri) - 1)) == '/')
				{
					$this->uri = substr($this->uri, 0, -1);
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
	

	/**
	 * getUriPosition function.
	 * 
	 * @brief returns the value of hte uri at the sepcific position
	 * @access public
	 * @param mixed $position
	 * @return If the position exists then it returns that value, else it returns null
	 */
	public function getUriPosition($position)
	{
		$return = null;
		if(count($this->uriArray) >= $position)
		{
			$position = $position-1;
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