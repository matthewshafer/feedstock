<?php

/*
Cache handles the caching of files..duh!
Ok so basically we create an md5 hash of the URI that was requested, remind me to convert it to lowercase or uppercase and do the check
once we have the md5 hash we can check to see if the file exists. if it doesnt exist we first return false from checkExists.
once the html is generated elsewhere we get that sent to us and we then create a file with that hash we generated earlier.
Sonce checkExists happens at the begining of the generation we can end up not hitting any MYSQL calls.
Now if we wanted to do something stats wise we could either hit an external file or not cache the header and footer and just cache the content.
But since Cache does not echo data we can send it whatever we want and then put the pieces together elsewhere.
*/

class cache
{
	private $cacheloc = null;
	private $uri = null;
	private $urimd5 = null;
	private $fileloc = null;
	// should make this a variable in the config file and then pass it in to Cache
	// leaving it here for now for creating
	private $cacheExpireTime = 1400;
	
	// we are assuming that the cacheloc is writeable and currently exists
	// we should probably add some checking to create it or alert that it isnt writeable
	public function __construct($uri)
	{
		// setting up the variables
		$this->cacheloc = V_BASELOC . "/private/cache/";
		//echo $this->cacheloc;
		$this->uri = $uri;
		// magic quotes?
		if(get_magic_quotes_gpc())
			$this->uri = stripcslashes($this->uri);
		$this->urimd5 = md5($this->uri);
		$this->fileloc = $this->cacheloc . "/" . $this->urimd5;
	}
	
	// returns true if the current file is valid and not expired
	// returns false if no cache exists or it is expired
	public function checkExists()
	{	
		$return = null;
		
		if(file_exists($this->fileloc))
		{
			//going to want to get file access date
			$filetime = filemtime($this->fileloc);
			// going to check to see if the file was last modified longer than the expire time
			if((time() - $filetime) > $this->cacheExpireTime)
			{
				// I could always not delete the file, i could overwrite the data in the file with new data
				// that might yield higher performance from the filesystem
				$this->deleteCachedFile();
				$return = false;
			}
			else
			{
				$return = true;
			}
		}
		else
		{
			$return = false;
		}
		
		return $return;
	}
	
	// simply unlinks the file
	private function deleteCachedFile()
	{
		unlink($this->fileloc);
	}
	
	// writes a file based on the data that is passed in, it uses the md5 hash of the uri to generate the filename
	public function writeCachedFile($data)
	{
		if($data != null)
		{
			$file = fopen($this->fileloc, 'w') or die("Can not write cached file");
			fwrite($file, $data);
			fclose($file);
		}
	}
	
	// returns the cached data, uses the md5 of the uri to open up the file
	public function getCachedData()
	{
		$file = fopen($this->fileloc, 'r') or die("Somehow the cached file doesn't exist now");
		$fileData = fread($file, filesize($this->fileloc));
		fclose($file);
		return $fileData;
	}
	
	// allows us to purge all files from the cache
	public function purgeCache()
	{
		$dir = opendir($this->cacheLoc);
		
		while($file = readdir($dir))
		{
			if($file != "." && $file != "..")
			{
				unlink($this->cacheLoc . "/" . $file);
			}
		}
		closedir($dir);
	}
}


?>