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
	private $uri = null;
	private $urimd5 = null;
	private $tmp = null;
	
	
	// new vars
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
		$this->expireTime = $expireTime;

	}
	
	
	// returns true if the current file is valid and not expired
	// returns false if no cache exists or it is expired
	/**
	 * checkExists function.
	 * 
	 * @brief Returns true if the current file exists and is not expired.
	 * @brief Returns false if no cache exists or is expired (this is saved in a var locally just in case the database is down, this way we can still serve content).
	 * @access public
	 * @return Boolean
	 */
	public function checkExists($lookup)
	{	
		$return = null;
		
		$lookup = $this->cacheLoc . $lookup;
		
		if(file_exists($lookup))
		{
			$tmp = $this->getCachedDataPrivate($lookup);
			//going to want to get file access date
			$filetime = filemtime($lookup);
			// going to check to see if the file was last modified longer than the expire time
			if((time() - $filetime) > $this->expireTime)
			{
				// I could always not delete the file, i could overwrite the data in the file with new data
				// that might yield higher performance from the filesystem
				$this->deleteCachedFile($lookup);
				$return = false;
			}
			else
			{
				if($tmp == null)
				{
					$return = false;
				}
				else
				{
					$return = true;
				}
			}
		}
		else
		{
			$return = false;
		}
		
		return $return;
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
	
	/**
	 * writeCachedFile function.
	 * 
	 * @brief writes the cached file with the data passed in.  If the database happen's to be down we are going to use the previous cache file if we have that saved.
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function writeCachedFile($data)
	{
		if($data != null)
		{
			$file = fopen($this->fileloc, 'w') or die("Can not write cached file");
			
			if(flock($file, LOCK_EX))
			{	
				if($data == "Unable to connect to the Database Server" or $data == "Unable to connect to the database" and $this->tmp != null)
				{
					fwrite($file, $this->tmp);
				}
				else
				{
					fwrite($file, $data);
					$this->tmp = $data;
				}
				
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
		return $this->tmp;
	}
	
	private function getCachedDataPrivate($lookup)
	{
		// switch this to throw an exception
			$file = @fopen($lookup, 'r') or die("Somehow the cached file doesn't exist now");
			flock($file, LOCK_SH);
			$size = filesize($lookup);
			if($size != 0)
			{
				$fileData = fread($file, filesize($lookup));
			}
			else
			{
				$fileData = null;
			}
			flock($file, LOCK_UN);
			fclose($file);
			
			return $fileData;
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
}
?>