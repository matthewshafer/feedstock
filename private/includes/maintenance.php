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
 	
 	public function __construct($loc, $filename)
 	{
 		$fileLoc = sprintf("%s/%s", $loc, $filename);
 		
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
 		
 		}
 		
 		return ob_get_clean();
 	}
 }
?>