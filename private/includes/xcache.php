<?php

class cache
{
	private $uri = null;
	private $urimd5 = null;
	private $prefixUri = null;
	
	
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
	}
	
	
	public function checkExists()
	{	
		$return = null;
		
		if(xcache_isset($this->prefixUri))
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
		if($data != null)
		{
			xcache_set($this->prefixUri, $data, F_EXPIRECACHETIME);
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
		xcache_unset_by_prefix($this->prefix);
	}
}
?>