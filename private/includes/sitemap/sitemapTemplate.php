<?php

class sitemapTemplate
{
	public function __construct()
	{
	
	}
	
	public function generateSitemap($sitemapData)
	{
		$returnSitemap = '<?xml version="1.0" encoding="UTF-8"?>
			<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
		';
		
		$endData = '</urlset>';
		
		foreach($sitemapData as $key)
		{
			$keyData = '
			<url>
				<loc>' . $key['URL'] . '</loc>
				<changefreq>' . $key['changeFreq'] . '</changefreq>
				<priority>' . $key['priority'] . '</priority>
			</url>
			';
			
			$returnSitemap = sprintf("%s%s", $returnSitemap, $keyData);
		}
		
		$returnSitemap = sprintf("%s%s", $returnSitemap, $endData);
		
		return $returnSitemap;
	}
}
?>