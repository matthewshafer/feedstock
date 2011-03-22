<?php
/**
 * PostManager class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * handles all the post stuff.  We can make it sanitize stuff if needed.
 * 
 */
class PostManager
{
	private $postArray = array();
	private $haveVals = false;
	
	/**
	 * __construct function.
	 * 
	 * copies the post value array into our own array
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// it would be a good thing to sanitize the data and then put that in our own array
		foreach(array_keys($_POST) as $key)
		{
			if($key === "id")
			{
				$data = (int)$_POST[$key];
			}
			else if($key === "postorpagedata")
			{
				$data = htmlspecialchars($_POST[$key]);
			}
			else
			{
				$data = $_POST[$key];
			}
			
			$this->postArray[$key] = $data;
			$this->haveVals = true;
		}
		
		// allows me to examine the post arrays
		//print_r($this->postArray);
	}
	
	/**
	 * getPostType function.
	 * 
	 * @access public
	 * @return string|null string if the post type is set, false if it isn't
	 */
	public function getPostType()
	{
		$return = null;
		
		if(isset($this->postArray["type"]))
		{
			$return = $this->postArray["type"];
		}
		
		return $return;
	}
	
	
	/**
	 * checkPostVal function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @return boolean false if the value does not exist in the post array, true if it exists
	 */
	public function checkPostVal($name)
	{
		$return = false;
		
		if(isset($this->postArray[$name]))
		{
			$return = true;
		}
		
		return $return;
	}
	
	
	/**
	 * checkPostWithArray function.
	 * 
	 * @access public
	 * @param mixed $array
	 * @return boolean false if something in the array passed does not exist in the post array. true if everything exists in the post array
	 */
	public function checkPostWithArray($array)
	{
		$return = true;
		
		if($array === null)
		{
			$return = false;
		}
		
		foreach($array as $value)
		{
			if(!isset($this->postArray[$value]))
			{
				$return = false;
			}
		}
		
		return $return;
	}
	
	
	/**
	 * getPostByName function.
	 * 
	 * @access public
	 * @param mixed $lookup
	 * @return mixed|null null if the item does not exist in the post array. Something else if it does (depends on what it was stored as)
	 */
	public function getPostByName($lookup)
	{
		$return = null;
		if(isset($this->postArray[$lookup]))
		{
			$return = $this->postArray[$lookup];
		}
		
		return $return;
	}
	
	
	/**
	 * havePostValues function.
	 * 
	 * @access public
	 * @return boolean True if there are values in the postArray, false if there are not any
	 */
	public function havePostValues()
	{
		return $this->haveVals;
	}
}
?>