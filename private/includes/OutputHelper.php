<?php
/**
 * OutputHelper class.
 * 
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * Allows us to control output from the server while keeping all the output for static file caching
 * 
 */
class OutputHelper
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
	 * Starts an output buffer
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
	 * stores the output and flushes it
	 * @access public
	 * @return void
	 */
	public function flushBuffer()
	{
		$tmp = ob_get_contents();
		
		ob_flush();
		flush();
		
		if($tmp != false)
		{
			$this->pageStore .= $tmp;
		}
	}
	
	/**
	 * stopStoreFlushGetBuffer function.
	 * 
	 * stores, flushes, and returns the current buffer
	 * @access public
	 * @return string string containing all the data from the buffer
	 */
	public function stopStoreFlushGetBuffer()
	{
		$tmp = ob_get_contents();
		
		ob_end_flush();
		
		if($tmp != false)
		{
			$this->pageStore .= $tmp;
		}
				
		return $this->pageStore;
	}
	
	/**
	 * stopStoreGetBuffer function.
	 * 
	 * stores and returns the current buffer
	 * @access public
	 * @return string string containing all the data from the buffer
	 */
	public function stopStoreGetBuffer()
	{
		$tmp = ob_end_clean();
		
		
		if($tmp != false)
		{
			$this->pageStore .= $tmp;
		}
				
		return $this->pageStore;
	}
}
?>