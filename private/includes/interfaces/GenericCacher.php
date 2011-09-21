<?php

interface GenericCacher
{
	
	// $location is only used for specific types of caches, ex. FileCache
	public function __construct($prefix, $expireTime, $location = "");
	
	public function checkExists($lookup);
	
	public function getCachedData();
	
	// might need to switch the order and give one generic data
	// the static caches currently don't use $toHash
	public function writeCachedFile($name, $data);
	
	public function purgeCache();
	
	public function cacheWritable();
	
	public function clearStoredData();

}

?>