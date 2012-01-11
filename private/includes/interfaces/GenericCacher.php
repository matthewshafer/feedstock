<?php

/**
 * GenericCacher interface.
 * 
 * Interface that is required to be implemented by all the different types of cacher's
 * @author Matthew Shafer <matt@niftystopwatch.com>
*/
interface GenericCacher
{
	
	// $location is only used for specific types of caches, ex. FileCache
	public function __construct($prefix, $expireTime, $location = "");

	/**
	 * checkExists function.
	 * 
	 * Checks to see if a query exists in the database
	 * @access public
	 * @param mixed $lookup (default)
	 * @return boolean True if it exists, false if it doesn't
	*/
	public function checkExists($lookup);
	
	/**
	 * getCachedData function.
	 * 
	 * ets cached data from the cache
	 * @access public
	 * @return mixed
	*/
	public function getCachedData();
	
	// might need to switch the order and give one generic data
	// the static caches currently don't use $toHash
	public function writeCachedFile($name, $data);
	
	/**
	 * purgeCache function.
	 * 
	 * Purges the entire cache. Useful when a new post/page is created
	 * @access public
	 * @return void
	*/
	public function purgeCache();
	
	/**
	 * cacheWritable function.
	 * 
	 * Checks to see if the cache is writable
	 * @access public
	 * @return boolean True if writable, false if not writable
	*/
	public function cacheWritable();
	
	/**
	 * clearStoredData function.
	 * 
	 * Cleares the data that is stored inside the cacher's temporary array.
	 * @access public
	 * @return void
	*/
	public function clearStoredData();

}

?>