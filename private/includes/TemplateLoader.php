<?php
/**
 * TemplateLoader class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * This works by getting the theme file location from getThemeLoc()
 * we then include that file that was returned to which it calls stuff from template engine
 * but now since template engine is an object seperate from the theme it can't call it's private methods which is what we want
 *
 */
class TemplateLoader
{
	
	private $themeLocation = null;
	private $templateEngine = null;
	private $outputHelper = null;
	

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $themeLocation
	 * @param TemplateEngine $templateEngine
	 * @param OutputHelper $outputHelper
	 * @return void
	 */
	public function __construct($themeLocation, TemplateEngine $templateEngine, OutputHelper $outputHelper)
	{
		$this->themeLocation = $themeLocation;
		$this->templateEngine = $templateEngine;
		$this->outputHelper = $outputHelper;
	}
	
	/**
	 * render function.
	 * 
	 * @access public
	 * @return string string containing the output of the theme
	 */
	public function render()
	{	
		include $this->themeLocation;
		
		return $this->outputHelper->stopStoreFlushGetBuffer();
	}


}
?>