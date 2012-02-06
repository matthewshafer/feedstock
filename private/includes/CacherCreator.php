<?php
/**
 * CacherCreator class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 */
class CacherCreator
{
	private $prefix = "";
	private $cacheName = "";
	private $cacheObj = null;
	private $baseLocation = "";
	private $expireTime = 0;
	private $options = null;
	
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param string $cacheName
	 * @param string $prefix
	 * @param string $expireTime
	 * @param string $baseLocation
	 * @return void
	 */
	public function __construct($cacheName, $prefix, $expireTime, $baseLocation, $options = null)
	{
		$this->cacheName = $cacheName;
		$this->prefix = $prefix;
		$this->expireTime = $expireTime;
		$this->baseLocation = $baseLocation;
		$this->options = $options;
	}
	
	
	
	/**
	 * getCacher function.
	 * 
	 * @access public
	 * @return GenericCacher|null GenericCacher object if one was created or null if one was not
	 */
	public function getCacher()
	{
		return $this->cacheObj;
	}
	
	
	
	/**
	 * createCacher function.
	 * 
	 * Creates the cacher object
	 * @access public
	 * @return boolean False if cache is not created, true if is created
	 */
	public function createCacher()
	{
		$return = false;
		
		
		require_once("interfaces/GenericCacher.php");
		require_once("caching/" . $this->cacheName . ".php");
		
		$this->cacheObj = new $this->cacheName($this->prefix, $this->expireTime, $this->baseLocation . "/private/cache/", $this->options);
		
		if($this->cacheObj instanceof GenericCacher && $this->cacheObj->cacheWritable())
		{
			$return = true;
		}
		
		
		return $return;
	}
}

?>