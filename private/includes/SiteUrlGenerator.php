<?php


/**
 * SiteUrlGenerator class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * Generates the site's url
 */
class SiteUrlGenerator
{

	private $url;
	private $baseAddress;
	private $htaccess;
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $url
	 * @param mixed $baseAddress
	 * @param mixed $htaccess
	 * @return void
	 */
	public function __construct($url, $baseAddress, $htaccess)
	{
		$this->url = $url;
		$this->baseAddress = $baseAddress;
		$this->htaccess = $htaccess;
	}
	
	
	/**
	 * generateSiteUrl function.
	 * 
	 * generates the site's url
	 * @access public
	 * @return string url of the website
	 */
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
				$url = $url . "/" . $baseAddress;
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
			$url = $url . "/index.php";
		}
		
		return $url;
	}


}
?>