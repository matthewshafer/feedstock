<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<feed xmlns="http://www.w3.org/2005/Atom">
			
	<title><?php echo $this->templateEngine->getHtmlTitle();?></title>
	<link href="<?php echo $this->templateEngine->siteUrl();?>" />
	<link  rel="self" href="<?php echo $this->templateEngine->siteUrl("feed/atom/");?>" type="application/atom+xml" />
	
	<?php
		if($this->feedEngine->pubSubHubBubEnabled())
		{
	?>
			<link  rel="hub" href="<?php echo $this->feedEngine->pubSubHubBubSubscribeUrl();?>" />
	<?php
		}
	?>
			<updated><?php echo $this->templateEngine->lastUpdatedTime("c");?></updated>
			<author>
				<name><?php echo $this->feedEngine->getFeedAuthor();?></name>
				<uri><?php echo $this->templateEngine->siteUrl();?></uri>
				<email><?php echo $this->feedEngine->getFeedEmail();?></email>
			</author>
			<id><?php echo $this->templateEngine->siteUrl("feed/atom/");?></id>
			
	<?php
		while($this->templateEngine->haveNextPost())
		{
	?>
			<entry>
				<title><?php echo $this->templateEngine->getPostTitle();?></title>
				<id><?php echo $this->templateEngine->getPostURL();?></id>
				<published><?php echo $this->templateEngine->getPostTime("c");?></published>
				<updated><?php echo $this->templateEngine->getPostTime("c");?></updated>
				<link href="<?php echo $this->templateEngine->getPostUrl();?>"/>
				<summary><?php echo $this->templateEngine->getPostBody();?></summary>
				<content><?php echo $this->templateEngine->getPostBody();?></content>
			</entry>
		<?php
			}
		?>
			
</feed>