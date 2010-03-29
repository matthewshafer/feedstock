<!DOCTYPE html>
<head>
	<title></title>
</head
<?php
//echo "<br><br>this is the theme talking<br>";

//print_r($this->templateEngine->getPageData());

$this->templateEngine->generateTags();
$this->templateEngine->generateCategories();

while($this->templateEngine->postNext())
{
	echo "<br>";
	echo "Title: " . $this->templateEngine->getPostTitle();
	echo "<br>";
	echo "URI: " . $this->templateEngine->getPostURI();
	echo "<br>";
	echo "Author: " . $this->templateEngine->getPostAuthor();
	echo "<br>";
	echo "Body: " . $this->templateEngine->getPostBody();
	echo "<br>";
	echo "Tags: " . $this->templateEngine->getPostTags();
	echo "<br>";
	echo "Tags formatted: " . $this->templateEngine->getPostTagsFormatted();
	echo "<br>";
	echo "Categories: " . $this->templateEngine->getPostCats();
	echo "<br>";
	echo "Categories: " . $this->templateEngine->getPostCatsFormatted();
	echo "<br>";
	echo "Date: " . $this->templateEngine->getPostTime("m / d / y");
	echo "<br>";
	echo "<br>";
	
}

?>