<?php
class PubSubHubBub
{
	private $hubURL = null;
	private $hubValid = null;
	private $errorText = null;
	private $feedLoc = array();
	
	
	public function __construct($hubUrl, $siteUrl)
	{
		$this->hubURL = $hubUrl;
		echo $hubUrl;
		
		$rss = sprintf("%s/feed/", $siteUrl);
		$atom = sprintf("%s/feed/atom/", $siteUrl);
		
		if($this->checkValidURL($rss))
		{
			array_push($this->feedLoc, $rss);
		}
		
		if($this->checkValidURL($atom))
		{
			array_push($this->feedLoc, $atom);
		}
		
		//print_r($this->feedLoc);
		
		if(!isset($this->feedLoc[0]))
		{
			$this->errorText .= "Invalid feed locations, V_URL should start with http:// ";
		}
		
		if(!$this->checkValidURL($this->hubURL))
		{
			$this->errorText .= "Invalid Hub URL, you need http:// ";
		}
	}
	
	/**
	 * publish function.
	 * 
	 * @access public
	 * @return void
	 */
	public function publish()
	{
		$return = array();
		
		if($this->errorText == null)
		{
			$count = count($this->feedLoc);
			$tmpArr = array();
			$mh = curl_multi_init();
			$threads = 0;
			
			for($i = 0; $i < $count; $i++)
			{
				$postField = sprintf("%s%s%s", "hub.mode=publish", "&hub.url=", urlencode($this->feedLoc[$i]));
				$tmpArr[$i] = curl_init();
				$curlOpts = array(CURLOPT_URL => $this->hubURL, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postField, CURLOPT_USERAGENT => "Feedstock-PubSubHubBub/1.0ALPHA");
				curl_setopt_array($tmpArr[$i], $curlOpts);
				curl_multi_add_handle($mh, $tmpArr[$i]);
			}
			
			do
			{
				$n = curl_multi_exec($mh, $threads);
			} while($threads > 0);
			
			for($i = 0; $i < $count; $i++)
			{
				$info = curl_getinfo($tmpArr[$i]);
				//print_r($info);
				
				if($info['http_code'] == 204)
				{
					$return[$i] = true;
				}
				else
				{
					$return[$i] = false;
				}
				
				curl_multi_remove_handle($mh, $tmpArr[$i]);
				curl_close($tmpArr[$i]);
			}
			
			curl_multi_close($mh);
		}
		
		return $return;
	}
	
	/**
	 * checkValidURL function.
	 * 
	 * @access private
	 * @param mixed $url
	 * @return void
	 */
	private function checkValidURL($url)
	{
		$return = false;
		
		if(preg_match("/https?:\/\//", $url))
		{
			$return = true;
		}
		
		return $return;
	}
}
?>