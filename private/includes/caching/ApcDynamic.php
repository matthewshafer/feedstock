<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Used to cache the sql from the database so we can get the data without having to talk to the database, if it's cached
 * 
 */
 
class ApcDynamic
{
	private $prefix = null;
	private $prefixArr = null;
	private $store = array();
	private $storePos = -1;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->prefix = F_XCACHEPREFIX;
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
		$success = false;
		$store = null;
		
		// might need to make this so when we check we also reset the time on the array
		$store = apc_fetch($lookup, $success);
		
		if($success)
		{
			array_push($this->store, apc_fetch($lookup));
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
		//$tmp = $this->currentData;
		//$this->currentData = null;
		if($this->storePos > -1)
		{
			$tmp = array_pop($this->store);
			$this->storePos--;
			//print_r($tmp);
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
		$success = false;
		$store = null;
		
		//if($data != null)
		//{
			apc_store($toHash, $data, F_EXPIRECACHETIME);
			
			$store = apc_fetch($this->prefixArr, $success);
			
			if($success)
			{
				$tmp = $store;
			}
			
			if(!isset($tmp[$toHash]))
			{
				$tmp[$toHash] = $toHash;
				apc_store($this->prefixArr, $tmp, F_EXPIRECACHETIME);
			}
		//}
	}
	
	/**
	 * purgeCache function.
	 * 
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
}

?>