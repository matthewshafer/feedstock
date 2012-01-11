<?php
/**
 * SitemapCreator class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * Creates Sitemaps
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
	private $maxItems = 0;
	private $siteUrl = "";
	private $postPerPage;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param GenericDatabaseAdmin $$db
	 * @param mixed $path
	 * @param mixed $max
	 * @param string $siteUrl
	 * @param mixed $postPerPage
	 * @return void
	*/
	public function __construct(GenericDatabaseAdmin $db, $path, $max, $siteUrl, $postPerPage)
	{
		$this->database = $db;
		$this->sitemapLoc = $path;
		$this->maxItems = (int)$max;
		$this->siteUrl = $siteUrl;
		$this->postPerPage = $postPerPage;
		
		require_once("sitemap/SitemapTemplate.php");
		$this->sitemapTemplate = new SitemapTemplate();
		
		//require_once("Sitemap/siteindexTemplate.php");
		//$this->siteindexTemplate = new SiteindexTemplate();
	}
	
	/**
	 * generateSitemap function.
	 * 
	 * Generates a sitemap for the website
	 * @access public
	 * @return void
	*/
	public function generateSitemap()
	{
		
		$indexArray = $this->makeIndexArray();
		
		$pages = $this->database->getAllPagesSitemap();
		$pages = $this->formatAddData($pages, ".5", "monthly", "page");
		$this->totalLength = count($pages);
		
		$this->sitemapDataSoFar = array_merge($indexArray, $pages);
		
		$this->processGeneratedSitemapData();
		
		$posts = $this->database->getAllPostsSitemap();
		$posts = $this->formatAddData($posts, ".5", "monthly");
		$postCt = count($posts);
		$this->totalLength = $this->totalLength + $postCt;
		$num = $postCt / $this->postPerPage;
		$totalPostPages = ceil($num) - 1;
		$postPage = $this->makePostPageLinks($totalPostPages);
		$this->totalLength = $this->totalLength + count($postPage);
		
		$this->sitemapDataSoFar = array_merge($this->sitemapDataSoFar, $posts, $postPage);
		
		$this->processGeneratedSitemapData();
		
		
		
		// need to generate pages for categories and tags
		$categories = $this->database->listCategoriesOrTags(0);
		$categories = $this->formatAddData($categories, ".5", "monthly", "category");
		$this->totalLength = $this->totalLength + count($categories);
		
		$this->sitemapDataSoFar = array_merge($this->sitemapDataSoFar, $categories);
		// calling this after every block should allow us to use less memory as we can free the previous variables
		// we arent doing that yet because well things arent done
		$this->processGeneratedSitemapData();
		
		
		$tags = $this->database->listCategoriesOrTags(1);
		$tags = $this->formatAddData($tags, ".5", "monthly", "tag");
		$this->totalLength = $this->totalLength + count($tags);
		
		$this->sitemapDataSoFar = array_merge($this->sitemapDataSoFar, $tags);
		
		
		// final generation of sitemap
		$this->finalGenerateSitemapData();
		
	}
	
	private function processGeneratedSitemapData()
	{
		if($this->totalLength >= $this->maxItems)
		{
			$fileName = sprintf("sitemap%i.xml", $this->totalSitemaps);
			
			$maxSitemapData = array_splice($this->sitemapDataSoFar, 0, $this->maxItems);
			$this->writeFile($fileName, $this->sitemapTemplate->generateSitemap($maxSitemapData));
			
			$this->totalLength = $this->totalLength - $this->maxItems;
			$this->totalSitemaps++;
			
			$this->processGeneratedSitemapData();
		}
	}
	
	private function finalGenerateSitemapData()
	{
		$this->processGeneratedSitemapData();
		
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
		// this line needs to be fixed before this is finished
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
	
	private function formatAddData($data, $priority, $changeFreq, $type = null)
	{
			$dataLength = count($data);
			$return = array();
			
			for($i = 0; $i < $dataLength; $i++)
			{
				$return[$i] = array();
				if(isset($data[$i]["URIName"]))
				{
					if($type === "tag")
					{
						$data[$i]['URIName'] = $this->addTagToUri($data[$i]['URIName']);
					}
					else if($type === "category")
					{
						$data[$i]['URIName'] = $this->addCategoryToUri($data[$i]['URIName']);
					}
					else if($type === "page")
					{
						$data[$i]['URIName'] = $this->checkPageUri($data[$i]['URIName']);
					}
					
					$return[$i]['URL'] = $this->makeURL($data[$i]['URIName']);
				}
				else
				{
					if($type === "tag")
					{
						$data[$i]['URI'] = $this->addTagToUri($data[$i]['URI']);
					}
					else if($type === "category")
					{
						$data[$i]['URI'] = $this->addCategoryToUri($data[$i]['URI']);
					}
					else if($type === "page")
					{
						$data[$i]['URI'] = $this->checkPageUri($data[$i]['URI']);
					}
					
					$return[$i]['URL'] = $this->makeURL($data[$i]['URI']);
				}
				
				$return[$i]['priority'] = $priority;
				$return[$i]['changeFreq'] = $changeFreq;
			}
			
			return $return;
	}
	
	private function addTagToUri($uri)
	{
		return sprintf("tag/%s", $uri);
	}
	
	private function addCategoryToUri($uri)
	{
		return sprintf("category/%s", $uri);
	}
	
	private function checkPageUri($uri)
	{
		if($uri[0] === "/")
		{
			$uri = substr($uri, 1);
		}
		
		return $uri;
	}
	
	private function makeURL($uri)
	{
		$return = null;
		
		$return = sprintf("%s/%s", $this->siteUrl, $uri);
		
		return $return;
	}
	
	private function makePostPageLinks($pages)
	{
		$tempArray = array();
		
		for($i = 0; $i < $pages; $i++)
		{
			$tempArray[$i] = array();
			$tempArray[$i]['URL'] = $this->makeURL(sprintf("%s%s", "page/", $i + 1));
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
		
		if($data !== null)
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