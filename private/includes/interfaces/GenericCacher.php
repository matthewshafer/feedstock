<?php

interface GenericCacher
{

	public function __construct($prefix, $uri);
	
	public function checkExists($lookup);
	
	public function getCachedData();
	
	// might need to switch the order and give one generic data
	// the static caches currently don't use $toHash
	public function writeCachedFile($toHash, $data);
	
	public function purgeCache();
	
	public function cacheType();

}

?>