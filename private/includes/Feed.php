<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief generates either an rss2.0 or atom feed
 * 
 */
		
		if($this->templateEngine->getFeedType() == "rss")
		{
			echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			
			if($this->templateEngine->pubSubHubBubEnabled())
			{
				echo "\t" . '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
			}
			else
			{
				echo "\t" . '<rss version="2.0">' . "\n";
			}
			echo "\t\t" . '<channel>' . "\n";
			if($this->templateEngine->pubSubHubBubEnabled())
			{
				echo "\t\t\t" . sprintf("%s%s%s", '<atom:link rel="hub" href="', $this->templateEngine->pubSubHubBubSubscribeUrl(), '"/>') . "\n";
			}
			echo "\t\t\t" . '<title>' . V_SITETITLE . '</title>' . "\n";
			echo "\t\t\t" . '<link>' . $this->templateEngine->siteUrl() . '</link>' . "\n";
			echo "\t\t\t" . '<description>' . V_DESCRIPTION . '</description>' . "\n";
			echo "\t\t\t" . '<lastBuildDate>' . $this->templateEngine->lastUpdatedTime("r") . '</lastBuildDate>' . "\n";
			echo "\t\t\t" . '<language>en-us</language>' . "\n";
				
			while($this->templateEngine->haveNextPost())
			{
				echo "\t\t\t" . '<item>' . "\n";
				echo "\t\t\t\t" . '<title>' . $this->templateEngine->getPostTitle() .'</title>' . "\n";
				echo "\t\t\t\t" . '<link>' . $this->templateEngine->getPostUrl() . '</link>' . "\n";
				echo "\t\t\t\t" . '<guid>' . $this->templateEngine->getPostUrl() . '</guid>' . "\n";
				echo "\t\t\t\t" . '<pubDate>' . $this->templateEngine->getPostTime("r") . '</pubDate>' . "\n";
				echo "\t\t\t\t" . '<description><![CDATA[ ' . $this->templateEngine->getPostBodyHTML() . ']]></description>' . "\n";
				echo "\t\t\t" . '</item>' . "\n";
			}
				
			echo "\t\t" . '</channel>' . "\n";
			echo "\t" . '</rss>';
				
		}
		else if($this->templateEngine->getFeedType() == "atom")
		{
			echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
			echo "\t" . '<feed xmlns="http://www.w3.org/2005/Atom">' . "\n";
			
			echo "\t\t" . '<title>' . V_SITETITLE . '</title>' . "\n";
			echo "\t\t" . '<link href="' . $this->templateEngine->siteUrl() . '" />' . "\n";
			echo "\t\t" . '<link  rel="self" href="' . $this->templateEngine->siteUrl("feed/atom/") . '" type="application/atom+xml" />' . "\n";
			if($this->templateEngine->pubSubHubBubEnabled())
			{
				echo "\t\t" . sprintf("%s%s%s", '<link  rel="hub" href="', $this->templateEngine->pubSubHubBubSubscribeUrl(), '" />') . "\n";
			}
			echo "\t\t" . '<updated>' . $this->templateEngine->lastUpdatedTime("c") . '</updated>' . "\n";
			echo "\t\t" . '<author>' . "\n";
			echo "\t\t\t" . '<name>'. $this->templateEngine->getFeedAuthor() . '</name>' . "\n";
			echo "\t\t\t" . '<uri>' . $this->templateEngine->siteUrl() . '</uri>' . "\n";
			echo "\t\t\t" . '<email>' . $this->templateEngine->getFeedEmail() . '</email>' . "\n";
			echo "\t\t" . '</author>' . "\n";
			echo "\t\t" . '<id>' . $this->templateEngine->siteUrl("feed/atom/") . '</id>' . "\n";
			
			
			while($this->templateEngine->haveNextPost())
			{
				echo "\t\t" . '<entry>' . "\n";
				echo "\t\t\t" . '<title>' . $this->templateEngine->getPostTitle() .'</title>' . "\n";
				echo "\t\t\t" . '<id>' . $this->templateEngine->getPostURL() . '</id>' . "\n";
				echo "\t\t\t" . '<published>' . $this->templateEngine->getPostTime("c") . '</published>' . "\n";
				echo "\t\t\t" . '<updated>' . $this->templateEngine->getPostTime("c") . '</updated>' . "\n";
				echo "\t\t\t" . '<link href="' . $this->templateEngine->getPostUrl() . '"/>' . "\n";
				echo "\t\t\t" . '<summary>'. $this->templateEngine->getPostBody() . '</summary>' . "\n";
				echo "\t\t\t" . '<content>'. $this->templateEngine->getPostBody() . '</content>' . "\n";
				echo "\t\t" . '</entry>' . "\n";
			}
			
			echo '</feed>';
		}


?>