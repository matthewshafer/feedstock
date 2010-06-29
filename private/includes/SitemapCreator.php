<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Creates Sitemaps
 * 
 */
class SitemapCreator
{
	private $database;
	private $sitemapLoc;
	private $totalLength = 0;
	private $totalSitemaps = 0;
	private $sitemapDataSoFar;
	private $sitemapTemplate;
	private $siteindexTemplate;
	
	public function __construct($db)
	{
		$this->database = $db;
		$this->sitemapLoc = V_BASELOC . "/public/";
		
		require_once("Sitemap/SitemapTemplate.php");
		$this->sitemapTemplate = new SitemapTemplate();
		
		//require_once("Sitemap/siteindexTemplate.php");
		//$this->siteindexTemplate = new SiteindexTemplate();
	}
	
	public function generateSitemap()
	{
		
		$indexArray = $this->makeIndexArray();
		
		$pages = $this->database->getAllPagesSitemap();
		$pages = $this->formatAddData($pages, ".5", "monthly");
		$this->totalLength = $this->totalLen + count($pages);
		
		$this->sitemapDataSoFar = array_merge($indexArray, $pages);
		
		$this->processGeneratedSitemapData();
		
		$posts = $this->database->getAllPostsSitemap();
		$posts = $this->formatAddData($posts, ".5", "monthly");
		$postCt = count($posts);
		$this->totalLen = $this->totalLen + $postCt;
		$num = $postCt / F_POSTSPERPAGE;
		$totalPostPages = ceil($num) - 1;
		$postPage = $this->makePostPageLinks($totalPostPages);
		$this->totalLen = $this->totalLen + count($postPage);
		
		$this->sitemapDataSoFar = array_merge($this->sitemapDataSoFar, $posts, $postPage);
		
		$this->processGeneratedSitemapData();
		
		
		
		// need to generate pages for categories and tags
		$categories = $this->database->listCategoriesOrTags(0);
		$categories = $this->formatAddData($categories, ".5", "monthly");
		$this->totalLen = $this->totalLen + count($categories);
		
		$this->sitemapDataSoFar = array_merge($this->sitemapDataSoFar, $categories);
		// calling this after every block should allow us to use less memory as we can free the previous variables
		// we arent doing that yet because well things arent done
		$this->processGeneratedSitemapData();
		
		
		$tags = $this->database->listCategoriesOrTags(1);
		$tags = $this->formatAddData($tags, ".5", "monthly");
		$this->totalLen = $this->totalLen + count($tags);
		
		$this->sitemapDataSoFar = array_merge($this->sitemapDataSoFar, $tags);
		
		
		// final generation of sitemap
		$this->finalGenerateSitemapData();
		
	}
	
	private function processGeneratedSitemapData()
	{
		if($this->totalLength >= intval(F_SITEMAPMAXITEMS))
		{
			$fileName = sprintf("sitemap%i.xml", $this->totalSitemaps);
			
			$maxSitemapData = array_splice($this->sitemapDataSoFar, 0, F_SITEMAPMAXITEMS);
			$this->writeFile($fileName, $this->sitemapTemplate->generateSitemap($maxSitemapData));
			
			$this->totalLength = $this->totalLength - F_SITEMAPMAXITEMS;
			$this->totalSitemaps++;
			
			$this->processGeneratedSitemapData();
		}
	}
	
	private function finalGenerateSitemapData()
	{
		$this->processGenerateSitemapData();
		
		if($this->totalSitemaps > 0)
		{
			$fileName = sprintf("sitemap%i.xml", $this->totalSitemaps);
			
			$this->writeFile($fileName, $this->sitemapTemplate->generateSitemap($this->sitemapDataSoFar));
			
			$this->totalSitemaps++;
			
			// then write sitemap index
			
			$this->writeSiteindex();
		}
		else
		{
			$this->writeFile("sitemap.xml", $this->sitemapTemplate->generateSitemap($this->sitemapDataSoFar));
		}
	}
	
	private function writeSiteindex()
	{
		$fileLoc = sprintf("%s%s", $this->sitemapLoc, $name);
		
		$data = $this->siteindexTemplate->generateSiteindex($this->totalSitemaps);
		
		if($data != null)
		{
			if($file = fopen($fileLoc, 'w'))
			{
			
				if(flock($file, LOCK_EX))
				{	
					fwrite($file, $data);
					flock($file, LOCK_UN);
				}
				
				fclose($file);
			}
		}
	}
	
	private function formatAddData($data, $priority, $changeFreq)
	{
			$dataLength = count($data);
			$return = array();
			
			for($i = 0; $i < $dataLength; $i++)
			{
				$return[$i] = array();
				if(isset($data[$i]["URIName"]))
				{
					$return[$i]['URL'] = $this->makeURL($data[$i]['URIName']);
				}
				else
				{
					$return[$i]['URL'] = $this->makeURL($data[$i]['URI']);
				}
				
				$return[$i]['priority'] = $priority;
				$return[$i]['changeFreq'] = $changeFreq;
			}
			
			return $return;
	}
	
	private function makeURL($uri)
	{
		$return = null;
	
		if(!V_HTACCESS)
		{
			$return = sprintf("%s%s%s%s", V_URL, V_HTTPBASE, "index.php", $uri);
		}
		else
		{
			$httpBase = V_HTTPBASE;
			$httpBaseLength = strlen($httpBase);
			if($httpBaseLength > 0 && $httpBase[$httpBaseLength-1] == "/")
			{
				$httpBase = substr($httpBase, 0, -1);
			}
			
			if($uri != null)
			{
				$return = sprintf("%s%s%s", V_URL, $httpBase, $uri);
			}
		}
		
		return $return;
	}
	
	private function makePostPageLinks($pages)
	{
		$tempArray = array();
		
		for($i = 0; $i < $pages; $i++)
		{
			$tempArray[$i] = array();
			$tempArray[$i]['URL'] = $this->makeURL(sprintf("%s%s", "/page/", $i + 1));
			$tempArray[$i]['priority'] = ".5";
			$tempArray[$i]['changeFreq'] = "daily";
		}
		
		//print_r($tempArray);
		return $tempArray;
	}
	
	private function makeIndexArray()
	{
		$tempArray = array();
		
		$tempArray[0] = array();
		
		$tempArray[0]['URL'] = $this->makeURL("");
		$tempArray[0]['priority'] = ".5";
		$tempArray[0]['changeFreq'] = "daily";
		
		return $tempArray;
	}
	
	private function writeFile($name, $data)
	{
		$fileLoc = sprintf("%s%s", $this->sitemapLoc, $name);
		
		if($data != null)
		{
			if($file = fopen($fileLoc, 'w'))
			{
			
				if(flock($file, LOCK_EX))
				{	
					fwrite($file, $data);
					flock($file, LOCK_UN);
				}
				
				fclose($file);
			}
		}
	}
}
?>