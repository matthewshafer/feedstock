<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Allows us to control output from the server while keeping all the output for static file caching
 * 
 */
 
class outputHelper
{
	private $pageStore = "";
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->startBuffer();
	}
	
	/**
	 * startBuffer function.
	 * 
	 * @brief Starts an output buffer
	 * @access private
	 * @return void
	 */
	private function startBuffer()
	{
		ob_start();
	}
	
	/**
	 * flushBuffer function.
	 * 
	 * @brief stores the output and flushes it
	 * @access public
	 * @return void
	 */
	public function flushBuffer()
	{
		$tmp = ob_get_contents();
		
		ob_flush();
		
		if($tmp != false)
		{
			$this->pageStore = sprintf("%s%s", $this->pageStore, $tmp);
		}
	}
	
	/**
	 * stopStoreGetBuffer function.
	 * 
	 * @ 
	 * @access public
	 * @return String containing all the data from the buffer
	 */
	public function stopStoreGetBuffer()
	{
		$tmp = ob_get_contents();
		
		ob_end_flush();
		
		if($tmp != false)
		{
			$this->pageStore = sprintf("%s%s" $this->pageStore, $tmp);
		}
				
		return $this->pageStore;
	}
}
?>