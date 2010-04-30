<?php

class cacheHandler
{
	private $router = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $router
	 * @return void
	 */
	public function __construct($router)
	{
		$this->router = $router;
	}
	
	/**
	 * cacheMaker function.
	 * 
	 * @access public
	 * @return void
	 */
	public function cacheMaker()
	{
		require_once("caching/" . F_CACHENAME . ".php");
		$cacher = null;
		
		switch(F_CACHENAME)
		{
			case "filecache":
				$cacher = new filecache($this->router->fullURI());
			break;
			case "xcacheStatic":
				$cacher = new xcacheStatic($this->router->fullURI());
			break;
			case "xcacheDynamic":
				$cacher = new xcacheDynamic();
			break;
		}
		
		return $cacher;
	}
	
	/**
	 * cacheWriteableLoc function.
	 * 
	 * @access public
	 * @return void
	 */
	public function cacheWriteableLoc()
	{
		$return = false;
		
		switch(F_CACHENAME)
		{
			case "filecache":
				if(is_writable(V_BASELOC . "/private/cache"))
				{
					$return = true;
				}
			break;
			case "xcacheStatic":
				if(function_exists('xcache_get'))
				{
					$return = true;
				}
			break;
		}
		
		return $return;
	}
	
	/**
	 * cacheType function.
	 * 
	 * @access public
	 * @return void
	 */
	public function cacheType()
	{
		$return = "static";
		
		switch(F_CACHENAME)
		{
			case "xcacheDynamic":
				$return = "dynamic";
			break;
		}
		
		return $return;
	}
}

?>