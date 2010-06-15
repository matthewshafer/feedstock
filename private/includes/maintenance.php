<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief maintenance mode
 * 
 */
 
 class maintenance
 {
 	private $fileValid = false;
 	private $fileLocation = null;
 	private $outputHelper;
 	
	// changed the construct to we save having to do another sprintf statement
 	public function __construct($fileLoc, $outputHelper)
 	{
 		//$fileLoc = sprintf("%s/%s", $loc, $filename);
 		
 		$this->outputHelper = $outputHelper;
 		
 		if(file_exists($fileLoc) && is_readable($fileLoc))
 		{
 			$this->fileValid = true;
 			$this->fileLocation = $fileLoc;
 		}
 	}
 	
 	public function render()
 	{
 		
 		if($this->fileValid)
 		{
 			include($this->fileLocation);
 		}
 		else
 		{
 			echo 'The website is currently undergoing some maintenance. <br>Check back soon.';
 		}
 		
 		$this->outputHelper->flushBuffer();
 	}
 }
?>