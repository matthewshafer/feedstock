<?php
$this->templateEngine->getPostsIndex();
$this->templateEngine->generateTags();
$this->templateEngine->generateCategories();

require_once("header.php");

$this->outputHelper->flushBuffer();


while($this->templateEngine->postNext())
{
	echo '
	<a href="' . $this->templateEngine->getPostURL() . '">' . $this->templateEngine->getPostTitle() . '</a>';
	echo "<br>";
	echo "Author: " . $this->templateEngine->getPostAuthor();
	echo "<br>";
	echo "Body: " . $this->templateEngine->getPostBodyHTML();
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
echo $this->templateEngine->haveNextPostPageHTML();
echo "<br><br><br>";
echo $this->templateEngine->getFormattedCorralByName("test1234");
echo "<br><br>";
echo $this->templateEngine->getSnippetByName("about");

require_once("footer.php");
?>