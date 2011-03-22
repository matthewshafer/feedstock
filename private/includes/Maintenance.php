<?php
/**
 * Maintenance class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * maintenance mode
 * 
 */
 class Maintenance
 {
 	private $fileValid = false;
 	private $fileLocation = null;
 	private $outputHelper;
 	
	
 	/**
 	 * __construct function.
 	 * 
 	 * @access public
 	 * @param mixed $fileLoc
 	 * @param OutputHelper $outputHelper
 	 * @return void
 	 */
 	public function __construct($fileLoc, OutputHelper $outputHelper)
 	{	
 		$this->outputHelper = $outputHelper;
 		
 		if(file_exists($fileLoc) && is_readable($fileLoc))
 		{
 			$this->fileValid = true;
 			$this->fileLocation = $fileLoc;
 		}
 	}
 	
 	
 	/**
 	 * render function.
 	 * 
 	 * @access public
 	 * @return void
 	 */
 	public function render()
 	{
 		
 		if($this->fileValid)
 		{
 			include($this->fileLocation);
 		}
 		else
 		{
 			throw new exception('The website is currently undergoing some maintenance. <br>Check back soon.');
 		}
 		
 		$this->outputHelper->flushBuffer();
 	}
 }
?>