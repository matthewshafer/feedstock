<?php

class templateLoader
{
	
	private $themeLoc = null;
	private $templateEngine = null;

	public function __construct($templateEngine)
	{
		$this->templateEngine = $templateEngine;
		$this->themeLoc = $this->templateEngine->getThemeLoc();
	}

	public function render()
	{
		ob_start();
		include $this->themeLoc;
		return ob_get_clean();
	}


}
?>