<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief handles all the post stuff.  We can make it sanitize stuff if needed.
 * 
 */
class PostManager
{
	private $postArray = array();
	private $haveVals = false;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @brief copies the post value array into our own array
	 * @return void
	 */
	public function __construct()
	{
		// it would be a good thing to sanitize the data and then put that in our own array
		foreach(array_keys($_POST) as $key)
		{
			if($key == "id")
			{
				$data = intval($_POST[$key]);
			}
			else if($key == "postorpagedata")
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
	 * @return Post type, if none then null
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
	
	public function checkPostVal($name)
	{
		$return = false;
		
		if(isset($this->postArray[$name]))
		{
			$return = true;
		}
		
		return $return;
	}
	
	public function checkPostWithArray($array)
	{
		$return = true;
		
		if($array == null)
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
	
	public function getPostByName($lookup)
	{
		$return = null;
		if(isset($this->postArray[$lookup]))
		{
			$return = $this->postArray[$lookup];
		}
		
		return $return;
	}
	
	public function havePostValues()
	{
		return $this->haveVals;
	}
}
?>