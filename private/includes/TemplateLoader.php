<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief This works by getting the theme file location from getThemeLoc()
 * @brief we then include that file that was returned to which it calls stuff from template engine
 * @brief but now since template engine is an object seperate from the theme it can't call it's private methods which is what we want
 */
class TemplateLoader
{
	
	private $themeLoc = null;
	private $templateEngine = null;
	private $outputHelper = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $templateEngine
	 * @return void
	 */
	public function __construct($templateEngine, $outputHelper)
	{
		$this->templateEngine = $templateEngine;
		$this->outputHelper = $outputHelper;
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
		$this->themeLoc = $this->templateEngine->getThemeLocation();
		
		if(!$this->templateEngine->haveThemeError())
		{
			include $this->themeLoc;
		}
		else
		{
			echo $this->templateEngine->getThemeError();
		}
		
		return $this->outputHelper->stopStoreFlushGetBuffer();
	}


}
?>