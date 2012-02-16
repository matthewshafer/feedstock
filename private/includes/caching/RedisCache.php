<?php

class RedisCache implements GenericCacher
{
	private $prefix = null;
	private $store = array();
	private $storePos = -1;
	private $expireTime;
	private $redisAddress = null;
	private $redisPort = null;
	private $redisDatabase = null;
	private $redisStore;


	/**
	 * __construct function.
	 * 
	 * Creates the RedisCache. $location should be the database you would like to use inside redis. 
	 * $options is an array of redis options like serverAddress, serverPort, etc
	 * @access public
	 * @param mixed $prefix
	 * @param mixed $expireTime
	 * @param string $location (default: 0)
	 * @param array $options
	 * @return void
	 */
	public function __construct($prefix, $expireTime, $location = "0", $options = null)
	{
		$this->prefix = $prefix;
		$this->expireTime = (int)$expireTime;
		$this->redisDatabase = (int)$location;

		if(isset($options['redisAddress']) && isset($options['redisPort']))
		{
			$this->redisAddress = $options['redisAddress'];
			$this->redisPort = $options['redisPort'];
		}
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
	 	$key = sha1($lookup);
	 	$return = false;

	 	try
	 	{
	 		if($this->redisStore->exists($key))
	 		{
	 			$tmp = $this->redisStore->get($key);
	 			$this->store[] = $tmp;
	 			$this->storePos++;
	 			$return = true;
	 		}
	 	}
	 	catch (Exception $e)
	 	{

	 	}

	 	return $return;
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
	
		if($this->storePos > -1)
		{
			$tmp = array_pop($this->store);
			$this->storePos--;
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
	 * Writes something to the cache. toHash is what you want the hash for that data to be
	 * @access public
	 * @param mixed $toHash
	 * @param mixed $data
	 * @return void
	 */
	public function writeCachedFile($toHash, $data)
	{
		$key = sha1($toHash);
		// storing the key in redis
		// redis can throw an exception so we are going to attempt to catch that and do nothing if we catch it.
		// if writes fail its not a huge deal at this point.  May possibly change things so it can fall back but we will have to see first.
		try
		{
			$this->redisStore->setex($key, $this->expireTime, $data);
		}
		catch (Exception $e)
		{
			
		}
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
		$this->redisStore->flushDB();
	}


	public function cacheWritable()
	{
		$ret = false;

		// checking if the redis extension is loaded
		if(extension_loaded("redis") && $this->redisAddress !== null && $this->redisPort !== null)
		{
			// trying to create the redis object
			try
			{
				$this->redisStore = new Redis();
				// if we are able to connect (timeout of 2.5 seconds) we set the cacheWritable to true
				if($this->redisStore->connect($this->redisAddress, $this->redisPort, 2.5))
				{
					$ret = true;

					if(!$this->redisStore->select($this->redisDatabase))
					{
						$ret = false;
					}

					if(!$this->redisStore->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY))
					{
						$ret = false;
					}

					if(!$this->redisStore->setOption(Redis::OPT_PREFIX, $this->prefix))
					{
						$ret = false;
					}
				}
			}
			// redis object failed, do nothing which means we return false and a cacher is not used
			catch (Exception $e)
			{
				$ret = false;
			}
		}

		return $ret;
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