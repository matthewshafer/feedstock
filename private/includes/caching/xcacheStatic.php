<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief caches using only xcache.  So no static files.  The downside is if the DB dies and xcache cleares the cache nothing is there to cache.
 * 
 */
class xcacheStatic
{
	private $uri = null;
	private $urimd5 = null;
	private $prefixUri = null;
	private $prefixArr = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $uri
	 * @return void
	 */
	public function __construct($uri)
	{
		// setting up the variables
		$this->cacheloc = V_BASELOC . "/private/cache/";
		//echo $this->cacheloc;
		$this->uri = $uri;
		// magic quotes?
		// im going to remove this soon because i never even checked for them in anything else
		if(get_magic_quotes_gpc())
			$this->uri = stripcslashes($this->uri);
		$this->urimd5 = md5($this->uri);
		$this->fileloc = $this->cacheloc . "/" . $this->urimd5;
		$this->prefix = sprintf("%s_", F_XCACHEPREFIX);
		$this->prefixUri = sprintf("%s%s", $this->prefix, $this->urimd5);
		$this->prefixArr = sprintf("%s%s", $this->prefix, "array");
		
		if(xcache_isset($this->prefixArr))
		{
			print_r(xcache_get($this->prefixArr));
		}
	}
	
	
	/**
	 * checkExists function.
	 * 
	 * @brief Checks if the cached file exists.
	 * @access public
	 * @return Boolean, True if exists False if it doesn't exist
	 */
	public function checkExists()
	{	
		$return = null;
		
		if(xcache_isset($this->prefixUri))
		{
				if(xcache_isset($this->prefixArr))
				{
					$tmp = xcache_get($this->prefixArr);
					xcache_set($this->prefixArr, $tmp, F_EXPIRECACHETIME);
				}
				$return = true;
		}
		else
		{
			if(xcache_isset($this->prefixArr))
			{
				$tmp = xcache_get($this->prefixArr);
				
				if(isset($tmp[$this->prefixUri]))
				{
					unset($tmp[$this->prefixUri]);
					
					xcache_set($this->prefixArr, $tmp, F_EXPIRECACHETIME);
				}
			}
			$return = false;
		}
		
		return $return;
	}
	
	/**
	 * deleteCachedFile function.
	 * 
	 * @brief Deletes the current cache file
	 * @access private
	 * @return void
	 */
	private function deleteCachedFile()
	{
		xcache_unset($this->prefixUri);
	}
	
	/**
	 * writeCachedFile function.
	 * 
	 * @brief writes the cached file with the data passed in.  If the database happen's to be down we are going to use the previous cache file if we have that saved.
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function writeCachedFile($data)
	{
		$tmp = array();
		
		if($data != null)
		{
				xcache_set($this->prefixUri, $data, F_EXPIRECACHETIME);
				
				if(xcache_isset($this->prefixArr))
				{
					$tmp = xcache_get($this->prefixArr);
				}
				
				if(!isset($tmp[$this->prefixUri]))
				{
					$tmp[$this->prefixUri] = $this->prefixUri;
					xcache_set($this->prefixArr, $tmp, F_EXPIRECACHETIME);
				}
		}
	}
	
	/**
	 * getCachedData function.
	 * 
	 * @brief Returns the cached page data for us to display.
	 * @access public
	 * @return String
	 */
	public function getCachedData()
	{
		return xcache_get($this->prefixUri);
	}
	
	/**
	 * purgeCache function.
	 * 
	 * @brief Allows us to purge everything in the cache. Something for admin's only.
	 * @access public
	 * @return void
	 */
	public function purgeCache()
	{
		if(xcache_isset($this->prefixArr))
		{
			$tmp = xcache_get($this->prefixArr);
			
			foreach($tmp as $key)
			{
				xcache_unset($key);
			}
			
			xcache_unset($this->prefixArr);
		}		
	}

}
?>