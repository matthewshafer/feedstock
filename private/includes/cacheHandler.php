<?php

class cacheHandler
{
	private $router = null;
	
	public function __construct($router)
	{
		$this->router = $router;
	}
	
	
	public function cacheMaker()
	{
		require_once("caching/" . F_CACHENAME . ".php");
		$cacher = null;
		
		switch(F_CACHENAME)
		{
			case "filecache":
				$cacher = new filecache($this->router->fullURI());
			break;
			case "xcache":
				$cacher = new xcacheStatic($this->router->fullURI());
			break;
		}
		
		return $cacher;
	}
	
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
			case "xcache":
				if(function_exists('xcache_get'))
				{
					$return = true;
				}
			break;
		}
		
		return $return;
	}

}

?>