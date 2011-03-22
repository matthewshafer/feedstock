<?php
/**
 * Router class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * Handles simple routing of pages
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
	
	
	/**
	 * __construct function.
	 * 
	 * Sets everything up
	 * @access public
	 * @param mixed $htaccess
	 * @param mixed $base (default)
	 * @return void
	 */
	public function __construct($htaccess, $base)
	{
		$this->htaccess = $htaccess;
		$this->uri = $_SERVER['REQUEST_URI'];
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
		$this->base = $base;
	}
	
	
	/**
	 * buildRouting function.
	 * 
	 * Makes cool things happen.
	 * @access public
	 * @return void
	 */
	public function buildRouting()
	{
		// adds a trailing slash to the base address if one does not exist fixes problems later with exploding strings and stuff
		if(substr_compare($this->base, "/", -1) !== 0)
		{
			$this->base .= "/";
		}
		
		// removes a trailing slash from a URI. Solves issues with array's who are exploded having somethign extra at the end
		// we go in here when the uri is / and end up making the uri ""
		// in the next statement block's else we would set the uri to false if it were to be changed to "" in this statement
		// this isn't a problem because we set the uri correctly if the substr fails in the next statements else block.
		if(substr_compare($this->uri, "/", -1) === 0)
		{
			$this->uri = substr($this->uri, 0, -1);
		}
		
		
		// figure out what happens when using htaccess
		// if its null we are going to assume its /
		// explodes the uri around the base address
		// leaving us with the actual request uri
		if($this->base != "/" && $this->base != null)
		{
			$temp = explode($this->base, $this->uri);
			
			if(isset($temp[1]))
			{
				$this->uri = $temp[1];
			}
			else
			{
				$this->uri = "";
			}
		}
		// this else statement is here to strip off the first / as it will explode to nothing in an array
		// we end up having the database put the / back on when it is needed in things like URI
		else
		{
			// when the uri is / we have an issue here where the uri becomes false (because the substr fails and returns false) it should be an empty string
			//$this->uri = substr($this->uri, 1);
			
			// fixed the above issue by checking if the substr function returned false, if it did then we set $this->uri to an empty string (else it would be set to a boolean(false) value
			if(($this->uri = substr($this->uri, 1)) === false)
			{
				$this->uri = "";
				//printf("here\n");
			}
			
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
				
				if(isset($temp[1]))
				{
					$this->uri = $temp[1];
				}
				else
				{
					$this->uri = "/";
				}
				
				// this removes the trailing slash if one exists
				//if($this->uri[strlen($this->uri) - 1] === '/')
				// we have a function for this so im commenting this out for now
				//if(substr_compare($this->uri, "/", -1) === 0)
				//{
				//	$this->uri = substr($this->uri, 0, -1);
				//}
				
				// this removes the starting slash if it exists. useful because we are now exploding the string around index.php
				// needed because when we look stuff up in the db we add the starting slash
				if($this->uri[0] === '/')
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
	 * Returns the first part of the uri. /test/1234/hello would return test. / returns "" (an empty string).
	 * @access public
	 * @return string|null null if there is nothing/haven't called buildRouting()
	 */
	public function pageType()
	{
		return $this->firstPart;
	}
	
	/**
	 * fullURI function.
	 * 
	 * Returns the uri as a string.
	 * @access public
	 * @return string|null
	 */
	public function fullURI()
	{
		return $this->uri;
	}
	

	/**
	 * getUriPosition function.
	 * 
	 * returns the value of the uri at the sepcific position
	 * @access public
	 * @param mixed $position
	 * @return string|null null if the position does not exist. A string if the value at that position does exist
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
	 * returns the type of the request, GET/POST
	 * @access public
	 * @return string returns what is returned by $_SERVER['REQUEST_METHOD']
	 */
	public function requestMethod()
	{
		return $this->requestMethod;
	}
	
	/**
	 * uriLength function.
	 * 
	 * Returns the number of items in the uri.  /test/123/afs/ would return 3.
	 * @access public
	 * @return int
	 */
	public function uriLength()
	{
		return count($this->uriArray);
	}
	
	/**
	 * searchURI function.
	 * 
	 * Allows you to search the URI to find something. if the uri was /test123/hello/sup and you searched for hello you get 2
	 * @access public
	 * @param mixed $find
	 * @return int integer that is the position of the search in the URI, -1 if it doesn't exist
	 */
	public function searchURI($find)
	{
		$return = -1;
		
		$count = count($this->uriArray);
		
		for($i = 0; $i < $count; $i++)
		{
			if($this->uriArray[$i] === $find)
			{
				$return = $i + 1;
				break;
			}
		}
		
		return $return;
	}
	
	
	/**
	 * evenURIParts function.
	 * 
	 * @access public
	 * @return boolean true if the uri has an even number of parts, false if it doesn't
	 */
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
	 * Figures out what page we are on from the URI and returns what it figures out
	 * @access public
	 * @return int int containing the page offset
	 */
	public function getPageOffset()
	{
		// allows php to save the last value of $page between runs so we can short circuit some logic
		static $page = -1;
		$pagePos;
		$tmpPage = null;
		
		// short circuit the logic if the page offset has already been generated
		if($page === -1)
		{
			$pagePos = $this->searchURI('page');
			
			// casting a null to an int gives is 0 and if we have anything less than 0 as the page then we have an error. casting an string to an int also gives us 0
			// By testing the strlength we can see if someone is using a float as the page number and then throw an exception in the else if
			if($pagePos > -1 && (int)($tmpPage = $this->getUriPosition($pagePos + 1)) > 0 && strlen($tmpPage) === strlen((int)$tmpPage))
			{
				// casting the tmpPage to an int because it previously was not
				$page = (int)$tmpPage - 1;
			}
			// if the uri contains /page/ but not a number after it this exception get thrown
			else if($pagePos > -1)
			{
				throw new exception('URI does not contain a page number');
			}
			// lastly if there is no page we set the page number to the default, 0
			else
			{
				$page = 0;
			}
		}
		
		return $page;
	}
}

?>