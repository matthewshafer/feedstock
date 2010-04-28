<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Handles writing responses to the disk for fast retrival
 *
 */
class filecache
{
	private $cacheLoc = null;
	private $uri = null;
	private $urimd5 = null;
	private $fileloc = null;
	private $tmp = null;
	
	/**
	 * __construct function.
	 * 
	 * @brief Constructs the cache.  md5's the uri we are passed.
	 * @access public
	 * @param mixed $uri
	 * @return void
	 */
	public function __construct($uri)
	{
		// setting up the variables
		$this->cacheLoc = V_BASELOC . "/private/cache/";
		//echo $this->cacheloc;
		$this->uri = $uri;
		// magic quotes?
		// im going to remove this soon because i never even checked for them in anything else
		if(get_magic_quotes_gpc())
			$this->uri = stripcslashes($this->uri);
		$this->urimd5 = md5($this->uri);
		$this->fileloc = $this->cacheLoc . "/" . $this->urimd5;

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
	public function checkExists()
	{	
		$return = null;
		
		if(file_exists($this->fileloc))
		{
			$this->tmp = $this->getCachedDataPrivate();
			//going to want to get file access date
			$filetime = filemtime($this->fileloc);
			// going to check to see if the file was last modified longer than the expire time
			if((time() - $filetime) > F_EXPIRECACHETIME)
			{
				// I could always not delete the file, i could overwrite the data in the file with new data
				// that might yield higher performance from the filesystem
				$this->deleteCachedFile();
				$return = false;
			}
			else
			{
				if($this->tmp == null)
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
	private function deleteCachedFile()
	{
		if(file_exists($this->fileloc))
		{
			unlink($this->fileloc);
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
	
	private function getCachedDataPrivate()
	{
			$file = @fopen($this->fileloc, 'r') or die("Somehow the cached file doesn't exist now");
			flock($file, LOCK_SH);
			$size = filesize($this->fileloc);
			if($size != 0)
			{
				$fileData = fread($file, filesize($this->fileloc));
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