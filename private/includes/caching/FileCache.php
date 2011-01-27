<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Handles writing responses to the disk for fast retrival
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
	 * @brief Constructs the cache.  md5's the uri we are passed.
	 * @access public
	 * @param mixed $uri
	 * @return void
	 */
	public function __construct($prefix, $expireTime, $location = "")
	{
		// setting up the variables
		// location should end in a /
		$this->cacheLoc = $location;
		$this->prefix = $prefix;
		$this->expireTime = intval($expireTime);

	}
	
	
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
	
	/**
	 * deleteCachedFile function.
	 * 
	 * @brief Deletes the current cache file
	 * @access private
	 * @return void
	 */
	private function deleteCachedFile($lookup)
	{
		if(file_exists($lookup))
		{
			unlink($lookup);
		}
	}
	
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
	 * @brief Returns the cached page data for us to display.
	 * @access public
	 * @return String
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
	 * @brief Allows us to purge everything in the cache. Something for admin's only.
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
	
	public function cacheWritable()
	{
		$return = false;
		
		if(is_dir($this->cacheLoc) && is_writable($this->cacheLoc))
		{
			$return = true;
		}
		
		return $return;
	}
}
?>