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
	private $router;
	private $generatedSiteUrl = null;
	private $siteBase = null;
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $url
	 * @param mixed $baseAddress
	 * @param mixed $htaccess
	 * @param Router $router
	 * @return void
	 */
	public function __construct($url, $baseAddress, $htaccess, Router $router)
	{
		$this->url = $url;
		$this->baseAddress = $baseAddress;
		$this->htaccess = $htaccess;
		$this->router = $router;
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
	
		if($this->generatedSiteUrl === null)
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
			
			// the site base should not contain the index.php file
			$this->siteBase = $url;
			
			if(!$this->htaccess)
			{
				$url = $url . "/index.php";
			}
			
			// storing the result of the work in the private generatedSiteUrl. Allows us to be efficient if this is called multiple times
			$this->generatedSiteUrl = $url;
		}
		
		return $this->generatedSiteUrl;
	}
	
	/**
	 * generateSiteBaseUrl function.
	 * 
	 * Generates the base url for the website. EX http://niftystopwatch.com/
	 * @access public
	 * @return string Url of the website
	*/
	public function generateSiteBaseUrl()
	{
		if($this->siteBase === null)
		{
			$this->generateSiteUrl();
		}
		
		return $this->siteBase;
	}
	
	/**
	 * currentAddressWithoutPageUrl function.
	 * 
	 * gets the current address without /page/1 or various other page numbers
	 * @access public
	 * @return string url of the address
	*/
	public function currentAddressWithoutPageUrl()
	{
		// need to subract 2 because getUriPart goes by array indexes which start at 0. if they started at 1 we could just subtract 1
		// but since searchURI gives us back a number where 1 would mean the 0 index in the array we need to subtract 2
		// i kept getUriPart different from the rest because i wanted it to be more like the substr function in php
		$pagePosition = $this->router->searchURI("page") - 2;
		
		if($pagePosition < -1)
		{
			$cleanUri = $this->router->fullUri() . "/";
			
			// adds a slash to the begining if one does not exist. useful for category and tag pages
			if($cleanUri[0] !== "/")
			{
				$cleanUri = "/" . $cleanUri;
			}
		}
		else if($pagePosition === -1)
		{
			$cleanUri = "/";
		}
		else
		{
			$cleanUri = $this->router->getUriPart(0, $pagePosition);
		}
		
		$cleanUri = $this->generateSiteUrl() . $cleanUri;
		
		return $cleanUri;
	}


}
?>