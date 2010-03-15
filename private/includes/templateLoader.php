<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief This works by getting the theme file location from getThemeLoc()
 * @brief we then include that file that was returned to which it calls stuff from template engine
 * @brief but now since template engine is an object seperate from the theme it can't call it's private methods which is what we want
 */
class templateLoader
{
	
	private $themeLoc = null;
	private $templateEngine = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $templateEngine
	 * @return void
	 */
	public function __construct($templateEngine)
	{
		$this->templateEngine = $templateEngine;
		//$this->themeLoc = $this->templateEngine->getThemeLoc();
	}
	
	/**
	 * render function.
	 * 
	 * @access public
	 * @return Generated html
	 */
	public function render()
	{
		$this->themeLoc = $this->templateEngine->getThemeLoc();
		ob_start();
		include $this->themeLoc;
		return ob_get_clean();
	}


}
?>