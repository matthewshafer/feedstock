<?php
/**
 * TemplateLoaderAdmin class.
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * This is the older version of TemplateLoader
 * Since I rewrote the front end templateEngine that TemplateLoader changed.
 * This one is here to allow the admin part of feedstock to still work.
 * I plan on changing the admin part sometime in the near future so this might change then
 */
class TemplateLoaderAdmin
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

		include $this->themeLoc;

		return $this->outputHelper->stopStoreFlushGetBuffer();
	}


}
?>