<?php

trait CacherStoredData
{
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