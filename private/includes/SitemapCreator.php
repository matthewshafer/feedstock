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
	private $totalLen = 0;
	private $totalSitemaps = 0;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->sitemapLoc = V_BASELOC . "/public/";
	}
	
	public function generateSitemap()
	{
		require_once("Sitemap/sitemapTemplate.php");
		$sitemapTemplate = new SitemapTemplate();
		
		$indexArr = $this->makeIndexArray();
		
		$pages = $this->db->getAllPagesSitemap();
		$pages = $this->formatAddData($pages, ".5", "monthly");
		$this->totalLen = $this->totalLen + count($pages);
		
		
		$posts = $this->db->getAllPostsSitemap();
		$posts = $this->formatAddData($posts, ".5", "monthly");
		$postCt = count($posts);
		$this->totalLen = $this->totalLen + $postCt;
		$num = $postCt / F_POSTSPERPAGE;
		$totalPostPages = ceil($num) - 1;
		$postPage = $this->makePostPageLinks($totalPostPages);
		
		
		
		// need to generate pages for categories and tags
		$categories = $this->db->listCategoriesOrTags(0);
		$categories = $this->formatAddData($categories, ".5", "monthly");
		$this->totalLen = $this->totalLen + count($categories);
		
		
		$tags = $this->db->listCategoriesOrTags(1);
		$tags = $this->formatAddData($tags, ".5", "monthly");
		$this->totalLen = $this->totalLen + count($tags);
		
		$finalData = array_merge($indexArr, $postPage, $posts, $pages, $categories, $tags);
		
		//print_r($finalData);
		
		$sitemapOutput = $sitemapTemplate->generateSitemap($finalData);
		
		//echo $sitemapOutput;
		
		$this->writeFile("sitemap.xml", $sitemapOutput);
		
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