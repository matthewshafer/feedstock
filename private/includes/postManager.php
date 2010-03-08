<?php
/**
 * postManager class.
 * @brief handles post requests.  Sanitizes data based on what we want from the post
 */
class postManager
{
	private $postArray = array();
	
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
			if($key == "ID")
			{
				$data = intval($_POST[$key]);
			}
			else if($key == "POSTORPAGEDATA")
			{
				$data = htmlspecialchars($_POST[$key]);
			}
			else
			{
				$data = $_POST[$key];
			}
			
			$this->postArray[$key] = $data;
		}
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
		
		if(isset($this->postArray["TYPE"]))
		{
			$return = $this->postArray["TYPE"];
		}
		
		return $return;
	}
}
?>