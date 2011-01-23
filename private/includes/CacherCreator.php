<?php

class CacherCreator
{
	private $prefix = "";
	private $cacheName = "";
	private $cacheObj = null;
	private $baseLocation = "";
	private $expireTime = 0;
	
	
	
	public function __construct($cacheName, $prefix, $expireTime, $baseLocation)
	{
		$this->cacheName = $cacheName;
		$this->prefix = $prefix;
		$this->expireTime = $expireTime;
		$this->baseLocation = $baseLocation;
	}
	
	
	
	public function getCacher()
	{
		return $this->cacheObj;
	}
	
	
	
	public function createCacher()
	{
		$return = false;
		
		
		require_once("interfaces/GenericCacher.php");
		require_once("caching/" . $this->cacheName . ".php");
		
		$this->cacheObj = new $this->cacheName($this->prefix, $this->expireTime, $this->baseLocation . "/private/cache/");
		
		if($this->cacheObj instanceof GenericCacher && $this->cacheObj->cacheWritable())
		{
			$return = true;
		}
		
		
		return $return;
	}
}

?>