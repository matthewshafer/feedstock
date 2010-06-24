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
			
			if(F_PUBSUBHUBBUB)
			{
				echo "\t" . '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
			}
			else
			{
				echo "\t" . '<rss version="2.0">' . "\n";
			}
			echo "\t\t" . '<channel>' . "\n";
			if(F_PUBSUBHUBBUB)
			{
				echo "\t\t\t" . sprintf("%s%s%s", '<atom:link rel="hub" href="', F_PUBSUBHUBBUBSUBSCRIBE, '"/>') . "\n";
			}
			echo "\t\t\t" . '<title>' . V_SITETITLE . '</title>' . "\n";
			echo "\t\t\t" . '<link>' . $this->templateEngine->siteURL() . '</link>' . "\n";
			echo "\t\t\t" . '<description>' . V_DESCRIPTION . '</description>' . "\n";
			echo "\t\t\t" . '<lastBuildDate>' . $this->templateEngine->lastUpdated("r") . '</lastBuildDate>' . "\n";
			echo "\t\t\t" . '<language>en-us</language>' . "\n";
				
			while($this->templateEngine->postNext())
			{
				echo "\t\t\t" . '<item>' . "\n";
				echo "\t\t\t\t" . '<title>' . $this->templateEngine->getPostTitle() .'</title>' . "\n";
				echo "\t\t\t\t" . '<link>' . $this->templateEngine->getPostURL() . '</link>' . "\n";
				echo "\t\t\t\t" . '<guid>' . $this->templateEngine->getPostURL() . '</guid>' . "\n";
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
			echo "\t\t" . '<link href="' . $this->templateEngine->siteURL() . '" />' . "\n";
			echo "\t\t" . '<link  rel="self" href="' . $this->templateEngine->siteURL("feed/atom/") . '" type="application/atom+xml" />' . "\n";
			if(F_PUBSUBHUBBUB)
			{
				echo "\t\t" . sprintf("%s%s%s", '<link  rel="hub" href="', F_PUBSUBHUBBUBSUBSCRIBE, '" />') . "\n";
			}
			echo "\t\t" . '<updated>' . $this->templateEngine->lastUpdated("c") . '</updated>' . "\n";
			echo "\t\t" . '<author>' . "\n";
			echo "\t\t\t" . '<name>'. F_AUTHOR . '</name>' . "\n";
			echo "\t\t\t" . '<uri>' . $this->templateEngine->siteURL() . '</uri>' . "\n";
			echo "\t\t\t" . '<email>' . F_AUTHOREMAIL . '</email>' . "\n";
			echo "\t\t" . '</author>' . "\n";
			echo "\t\t" . '<id>' . $this->templateEngine->siteURL("feed/atom/") . '</id>' . "\n";
			
			
			while($this->templateEngine->postNext())
			{
				echo "\t\t" . '<entry>' . "\n";
				echo "\t\t\t" . '<title>' . $this->templateEngine->getPostTitle() .'</title>' . "\n";
				echo "\t\t\t" . '<id>' . $this->templateEngine->getPostURL() . '</id>' . "\n";
				echo "\t\t\t" . '<published>' . $this->templateEngine->getPostTime("c") . '</published>' . "\n";
				echo "\t\t\t" . '<updated>' . $this->templateEngine->getPostTime("c") . '</updated>' . "\n";
				echo "\t\t\t" . '<link href="' . $this->templateEngine->getPostURL() . '"/>' . "\n";
				echo "\t\t\t" . '<summary>'. $this->templateEngine->getPostBody() . '</summary>' . "\n";
				echo "\t\t\t" . '<content>'. $this->templateEngine->getPostBody() . '</content>' . "\n";
				echo "\t\t" . '</entry>' . "\n";
			}
			
			echo '</feed>';
		}


?>