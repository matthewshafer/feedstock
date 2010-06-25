<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Creates Sitemaps
 * 
 */
class SitemapCreator
{
	private $db;
	private $sitemapLoc;
	private $totalLength = 0;
	private $totalSitemaps = 0;
	private $sitemapDataSoFar;
	private $sitemapTemplate;
	private $siteindexTemplate;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->sitemapLoc = V_BASELOC . "/public/";
		
		require_once("Sitemap/sitemapTemplate.php");
		$this->sitemapTemplate = new SitemapTemplate();
		
		//require_once("Sitemap/siteindexTemplate.php");
		//$this->siteindexTemplate = new SiteindexTemplate();
	}
	
	public function generateSitemap()
	{
		
		$indexArray = $this->makeIndexArray();
		
		$pages = $this->db->getAllPagesSitemap();
		$pages = $this->formatAddData($pages, ".5", "monthly");
		$this->totalLength = $this->totalLen + count($pages);
		
		$this->sitemapDataSoFar = array_merge($indexArray, $pages);
		
		$this->processGeneratedSitemapData();
		
		$posts = $this->db->getAllPostsSitemap();
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
		$categories = $this->db->listCategoriesOrTags(0);
		$categories = $this->formatAddData($categories, ".5", "monthly");
		$this->totalLen = $this->totalLen + count($categories);
		
		$this->sitemapDataSoFar = array_merge($this->sitemapDataSoFar, $categories);
		// calling this after every block should allow us to use less memory as we can free the previous variables
		// we arent doing that yet because well things arent done
		$this->processGeneratedSitemapData();
		
		
		$tags = $this->db->listCategoriesOrTags(1);
		$tags = $this->formatAddData($tags, ".5", "monthly");
		$this->totalLen = $this->totalLen + count($tags);
		
		$this->sitemapDataSoFar = array_merge($this->sitemapDataSoFar, $tags);
		
		
		// final generation of sitemap
		$this->finalGenerateSitemapData();
		
	}
	
	private processGeneratedSitemapData()
	{
		if($this->totalLength >= F_SITEMAPMAXITEMS)
		{
			$fileName = sprintf("sitemap%i.xml", $this->totalSitemaps);
			
			$maxSitemapData = array_splice($this->sitemapDataSoFar, 0, F_SITEMAPMAXITEMS);
			$this->writeFile($fileName, $this->sitemapTemplate->generateSitemap($maxSitemapData));
			
			$this->totalLength = $this->totalLength - F_SITEMAPMAXITEMS;
			$this->totalSitemaps++;
			
			$this->processGeneratedSitemapData();
		}
	}
	
	private finalGenerateSitemapData()
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
			$ct = count($data);
			$tmpArr = array();
			
			for($i = 0; $i < $ct; $i++)
			{
				$tmpArr[$i] = array();
				if(isset($data[$i]["URIName"]))
				{
					$tmpArr[$i]['URL'] = $this->makeURL($data[$i]['URIName']);
				}
				else
				{
					$tmpArr[$i]['URL'] = $this->makeURL($data[$i]['URI']);
				}
				
				$tmpArr[$i]['priority'] = $priority;
				$tmpArr[$i]['changeFreq'] = $changeFreq;
			}
			
			return $tmpArr;
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
			$tmp = V_HTTPBASE;
			$len = strlen($tmp);
			if($len > 0 && $tmp[$len-1] == "/")
			{
				$tmp = substr($tmp, 0, -1);
			}
			
			if($uri != null)
			{
				$return = sprintf("%s%s%s", V_URL, $tmp, $uri);
			}
		}
		
		return $return;
	}
	
	private function makePostPageLinks($pages)
	{
		$tmpArr = array();
		
		for($i = 0; $i < $pages; $i++)
		{
			$tmpArr[$i] = array();
			$tmpArr[$i]['URL'] = $this->makeURL(sprintf("%s%s", "/page/", $i + 1));
			$tmpArr[$i]['priority'] = ".5";
			$tmpArr[$i]['changeFreq'] = "daily";
		}
		
		//print_r($tmpArr);
		return $tmpArr;
	}
	
	private function makeIndexArray()
	{
		$tmpArr = array();
		
		$tmpArr[0] = array();
		
		$tmpArr[0]['URL'] = $this->makeURL("");
		$tmpArr[0]['priority'] = ".5";
		$tmpArr[0]['changeFreq'] = "daily";
		
		return $tmpArr;
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