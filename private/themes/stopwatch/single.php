<?php
$this->templateEngine->generateTags();
$this->templateEngine->generateCategories();
?>

<!DOCTYPE html>
<head>
	<title><?php echo $this->templateEngine->getHtmlTitle(); ?></title>
</head>

<body>
	<div id="Post">
		<div id="Title"></div>
			<a href="<?php echo $this->templateEngine->getPostURL(); ?>"><?php echo $this->templateEngine->getPostTitle(); ?></a>
		</div>
		<div id="Date">
			Date: <?php echo $this->templateEngine->getPostTime("m/d/y"); ?>
		</div>
		<div id="Author">
			Author: <?php echo $this->templateEngine->getPostAuthor(); ?>
		</div>
		<div id="PostBody">
			Body: <?php echo $this->templateEngine->getPostBodyHTML(); ?>
		</div>
		<div id="Tags">
			Tags: <?php echo $this->templateEngine->getPostTagsFormatted(); ?>
		</div>
		<div id="Categories">
			Categories: <?php echo $this->templateEngine->getPostCatsFormatted(); ?>
		</div>
	</div>


</body>