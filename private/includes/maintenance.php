<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief checks Ip address, it's super basic at the moment
 * 
 */
 
 class maintenance
 {
 	private $fileValid = false;
 	private $fileLocation = null;
 	
	// changed the construct to we save having to do another sprintf statement
 	public function __construct($fileLoc)
 	{
 		//$fileLoc = sprintf("%s/%s", $loc, $filename);
 		
 		if(file_exists($fileLoc) && is_readable($fileLoc))
 		{
 			$this->fileValid = true;
 			$this->fileLocation = $fileLoc;
 		}
 	}
 	
 	public function render()
 	{
 		ob_start();
 		
 		if($this->fileValid)
 		{
 			include($this->fileLocation);
 		}
 		else
 		{
 			echo 'The website is currently undergoing some maintenance. <br>Check back soon.';
 		}
 		
 		return ob_get_clean();
 	}
 }
?>