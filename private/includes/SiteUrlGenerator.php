<?php

class SiteUrlGenerator
{

	private $url;
	private $baseAddress;
	private $htaccess;
	
	
	public function __construct($url, $baseAddress, $htaccess)
	{
		$this->url = $url;
		$this->baseAddress = $baseAddress;
		$this->htaccess = $htaccess;
	}
	
	
	public function generateSiteUrl()
	{
		$url = $this->url;
		$baseAddress = $this->baseAddress;
		$urlLen = strlen($url);
		$baseLen = strlen($baseAddress);
		
		
		if($baseLen > 0 && $baseAddress[$baseLen - 1] === "/")
		{
			$baseAddress = substr($baseAddress, 0 , -1);
			$baseLen--;
		}
		
		if($baseLen > 0 && $baseAddress[0] === "/")
		{
			$baseAddress = substr($baseAddress, 1);
			$baseLen--;
		}
		
		if($urlLen > 0)
		{
			if($baseLen > 0 && $url[$urlLen - 1] === "/")
			{
				$url .= $baseAddress;
			}
			else if($baseLen > 0)
			{
				$url = sprintf("%s/%s", $url, $baseAddress);
			}
			else if($url[$urlLen - 1] === "/")
			{
				$url = substr($url, 0, -1);
			}
		}
		else
		{
			// switched to an exception since we are catching all of them already
			throw new exception("Invalid website URL");
		}
		
		if(!$this->htaccess)
		{
			$url = sprintf("%s/index.php", $url);
		}
		
		return $url;
	}


}
?>