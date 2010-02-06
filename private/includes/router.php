<?php
/**
* @file
* @author Matthew Shafer <matt@niftystopwatch.com>
* 
* @brief Handles simple routing of pages
*/

class router
{
	protected $uri = null;
	protected $firstPart = null;
	protected $uriArray = null;
	protected $htaccess = null;
	protected $requestMethod = null;
	
	
	/**
	* Constructor which sets up what is needed
	* 
	* @param htaccess If we are using this to remove index.php
	*/
	public function __construct($htaccess)
	{
		$this->htaccess = $htaccess;
		$this->uri = $_SERVER['REQUEST_URI'];
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
		$this->buildRouting();
	}
	
	/**
	* @brief buildRouting breaks down the URI and stores it in the uriArray
	*
	*
	*/
	private function buildRouting()
	{
		// figure out what happens when using htaccess
		if(V_HTTPBASE != "/")
		{
			$temp = explode(V_HTTPBASE, $this->uri);
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
	* pageType simply returns the first part of the array
	* we can do this because the first part of the URI is most likely going to contain to determine which page to load
	*/
	public function pageType()
	{
		return $this->firstPart;
	}
	
	/**
	* @brief fullURI returns the uri, formatted ofcourse
	*
	*/
	public function fullURI()
	{
		return $this->uri;
	}
	
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
	
	public function requestMethod()
	{
		return $this->requestMethod;
	}
	
	public function returnPart($position)
	{
		$return = null;
		
		if($position >= count($this->uriArray))
		{
			;
		}
		else
		{
			$return = $this->uriArray[$position];
		}
		
		return $return;
	}
	
	public function uriLength()
	{
		return count($this->uriArray);
	}
	
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