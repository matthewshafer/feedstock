<?php
$this->templateEngine->generateTags();
$this->templateEngine->generateCategories();
?>

<!DOCTYPE html>
<head>
	<title><?php echo $this->templateEngine->getHtmlTitle(); ?></title>
</head>

<body>
<?php
//echo "<br><br>this is the theme talking<br>";

//print_r($this->templateEngine->getPageData());

while($this->templateEngine->postNext())
{
	echo '<a href="' . $this->templateEngine->getPostURL() . '">' . $this->templateEngine->getPostTitle() . '</a>';
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
	echo "Date: " . $this->templateEngine->getPostTime("m/d/y");
	echo "<br>";
	echo "<br>";
	
}

?>

</body>