<?xml version="1.0" encoding="UTF-8"?>

	<?php		
		if($this->feedEngine->pubSubHubBubEnabled())
			{
	?>
				<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<?php
			}
			else
			{
	?>
				<rss version="2.0">
	<?php
			}
	?>
		
		<channel>
		
		<?php
			if($this->feedEngine->pubSubHubBubEnabled())
			{
		?>
				<atom:link rel="hub" href="<?php echo $this->feedEngine->pubSubHubBubSubscribeUrl();?>"/>
		<?php
			}
		?>
			<title><?php echo $this->templateEngine->getHtmlTitle();?></title>
			<link><?php echo $this->templateEngine->siteUrl();?></link>
			<description><?php echo $this->templateEngine->getSiteDescription();?></description>
			<lastBuildDate><?php echo $this->templateEngine->lastUpdatedTime("r");?></lastBuildDate>
			<language>en-us</language>
			
		<?php
			// only need <?php on the parts where we are executing php. so we only need to echo the return values
			while($this->templateEngine->haveNextPost())
			{
		?>
				<item>
					<title><?php echo $this->templateEngine->getPostTitle();?></title>
					<link><?php echo $this->templateEngine->getPostUrl();?></link>
					<guid><?php echo $this->templateEngine->getPostUrl();?></guid>
					<pubDate><?php echo $this->templateEngine->getPostTime("r");?></pubDate>
					<description><![CDATA[<?php echo $this->templateEngine->getPostBodyHTML();?>]]></description>
				</item>
		<?php
			}
		?>
				
		</channel>
	</rss>