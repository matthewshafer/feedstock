<?php
/**
 * Handles writing responses to the disk for fast retrival
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * 
 *
 */
class FileCache implements GenericCacher
{
	private $cacheLoc = null;
	private $prefix = null;
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
		// setting up the variables
		// location should end in a /
		$this->cacheLoc = $location;
		$this->prefix = $prefix;
		$this->expireTime = (int)$expireTime;

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
		$lookup = $this->cacheLoc . $this->prefix . sha1($lookup);
		$tmp = "";
		
		if(file_exists($lookup))
		{
			$tmp = $this->loadFile($lookup, $return);
			
			if($return)
			{
				$this->store[] = $tmp;
				$this->storePos++;
			}
		}
		
		return $return;
	}
	
	private function loadFile($lookup, &$return)
	{
		$data = "";
		
		if($this->expireTime > 0 && (($_SERVER['REQUEST_TIME'] - filemtime($lookup)) > $this->expireTime))
		{
			$this->deleteCachedFile($lookup);
			$return = false;
		}
		else
		{
			// file_exists is checked just before this function is called
			// calling it twice lowers the performance by a bit.
			if(($file = fopen($lookup, 'r')) != false)
			{
				flock($file, LOCK_SH);
				$size = filesize($lookup);
				
				if($size != 0)
				{
					$data = fread($file, $size);
					$data = unserialize($data);
					$return = true;
					
				}
				else
				{
					// this like probably never gets hit.  I'll look into it in the future and see if its really needed
					$this->deleteCachedFile($lookup);
				}
				
				flock($file, LOCK_UN);
				fclose($file);
			}
		}
		
		return $data;
	}
	
	
	private function deleteCachedFile($lookup)
	{
		if(file_exists($lookup))
		{
			unlink($lookup);
		}
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
		$toHash = $this->cacheLoc . $this->prefix . sha1($toHash);
		
		if($data != null && ($file = fopen($toHash, 'w')) != false)
		{
			if(flock($file, LOCK_EX))
			{
				$data = serialize($data);
				fwrite($file, $data);
				
				flock($file, LOCK_UN);
			}
			fclose($file);
		}
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
		$tmp = null;
		
		if($this->storePos > -1)
		{
			$tmp = array_pop($this->store);
			$this->storePos--;
		}
		
		return $tmp;
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
		$dir = opendir($this->cacheLoc);
		
		while($file = readdir($dir))
		{
			if($file != "." && $file != ".." && $file != ".svn")
			{
				unlink($this->cacheLoc . "/" . $file);
			}
		}
		closedir($dir);
	}
	
	/**
	 * cacheWritable function.
	 * 
	 * @access public
	 * @return boolean True if the cache is writable false if it is not
	 */
	public function cacheWritable()
	{
		$return = false;
		
		if(is_dir($this->cacheLoc) && is_writable($this->cacheLoc))
		{
			$return = true;
		}
		
		return $return;
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