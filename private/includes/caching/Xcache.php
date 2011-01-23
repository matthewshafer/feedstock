<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Used to cache the sql from the database so we can get the data without having to talk to the database, if it's cached
 * 
 */
class Xcache implements GenericCacher
{
	private $prefix = null;
	private $prefixArr = null;
	private $store = array();
	private $storePos = -1;
	private $expireTime;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct($prefix, $expireTime, $location = "")
	{
		$this->prefix = $prefix;
		$this->expireTime = $expireTime;
		$this->prefixArr = sprintf("%s%s", $this->prefix, "array");
	}
	
	/**
	 * checkExists function.
	 * 
	 * @access public
	 * @param mixed $lookup
	 * @return void
	 */
	public function checkExists($lookup)
	{
		$return = false;
		$lookup = sprintf("%s%s", $this->prefix, sha1($lookup));
		
		// might need to make this so when we check we also reset the time on the array
		if(xcache_isset($lookup))
		{
			
			array_push($this->store, xcache_get($lookup));
			$this->storePos++;
			
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * getCachedData function.
	 * 
	 * @access public
	 * @return void
	 */
	public function getCachedData()
	{
	
		if($this->storePos > -1)
		{
			$tmp = array_pop($this->store);
			$this->storePos--;
		}
		else
		{
			$tmp = null;
		}
		
		return $tmp;
	}
	
	/**
	 * writeCachedFile function.
	 * 
	 * @access public
	 * @param mixed $toHash
	 * @param mixed $data
	 * @return void
	 */
	public function writeCachedFile($toHash, $data)
	{
		$tmp = array();
		$toHash = sprintf("%s%s", $this->prefix, sha1($toHash));
		
		xcache_set($toHash, $data, $this->expireTime);
			
		if(xcache_isset($this->prefixArr))
		{
			$tmp = xcache_get($this->prefixArr);
		}
		
		if(!isset($tmp[$toHash]))
		{
			$tmp[$toHash] = $toHash;
			xcache_set($this->prefixArr, $tmp, $this->expireTime);
		}
	}
	
	/**
	 * purgeCache function.
	 * 
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
	
	
	public function cacheWritable()
	{
		$ret = false;
		
		if(function_exists("xcache_get"))
		{
			$ret = true;
		}
		
		return $ret;
	}
}
?>