<?php
/**
 * Apc caching class for feedstock
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * 
 * 
 */
class Apc implements GenericCacher
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
	 * @param mixed $prefix
	 * @param mixed $expireTime
	 * @param string $location (default: "")
	 * @return void
	 */
	public function __construct($prefix, $expireTime, $location = "")
	{
		$this->prefix = $prefix;
		$this->expireTime = $expireTime;
		$this->prefixArr = $this->prefix . 'array';
	}
	
	/**
	 * checkExists function.
	 * 
	 * Checks to see if something in the cache exists
	 * @access public
	 * @param mixed $lookup
	 * @return boolean True if it exists, false if it doesn't
	 */
	public function checkExists($lookup)
	{
		$return = false;
		$lookup = $this->prefix . sha1($lookup);
		$success = false;
		$store = null;
		
		// might need to make this so when we check we also reset the time on the array
		$store = apc_fetch($lookup, $success);
		
		if($success)
		{
			$this->store[] = $store;
			$this->storePos++;
			
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * getCachedData function.
	 * 
	 * Gets cached data from the cache. Data is stored when you call checkExists. This way we do one lookup rather than two
	 * @access public
	 * @return mixed
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
	 * Writes something to the cache. toHash is what you want the hash for that data to be
	 * @access public
	 * @param mixed $toHash
	 * @param mixed $data
	 * @return void
	 */
	public function writeCachedFile($toHash, $data)
	{
		$tmp = array();
		$toHash = $this->prefix . sha1($toHash);
		$success = false;
		$store = null;
		
		apc_store($toHash, $data, $this->expireTime);
		
		$store = apc_fetch($this->prefixArr, $success);
		
		if($success)
		{
			$tmp = $store;
		}
		
		if(!isset($tmp[$toHash]))
		{
			$tmp[$toHash] = $toHash;
		}
		
		apc_store($this->prefixArr, $tmp, $this->expireTime);
	}
	
	/**
	 * purgeCache function.
	 * 
	 * Purges the entire cache. Useful when a new post/page is created
	 * @access public
	 * @return void
	 */
	public function purgeCache()
	{
		$success = false;
		$store = null;
		
		$store = apc_fetch($this->prefixArr, $success);
		
		if($success)
		{
			foreach($store as $key)
			{
				apc_delete($key);
			}
			
			apc_delete($this->prefixArr);
		}		
	}
	
	
	/**
	 * cacheWritable function.
	 * 
	 * @access public
	 * @return boolean True if the cache is writable false if it is not
	 */
	public function cacheWritable()
	{
		$ret = false;
		
		if(function_exists("apc_store"))
		{
			$ret = true;
		}
		
		return $ret;
	}
	
	/**
	 * clearStoredData function.
	 * 
	 * Cleares the data that is stored inside the cacher's temporary array.
	 * @access public
	 *
	 */
	 public function clearStoredData()
	 {
	 	$this->store = array();
	 	$this->storePos = -1;
	 }
}

?>