<?php require_once("header.php"); ?>
<div id="postBox">
<?php 
printf("%s%s%s%s", "Post's containing the tag ", '"', $this->templateEngine->tagPostName(), '"');
foreach($this->templateEngine->getTheData() as $key)
{
	echo '<div class="singlePostBox">';
	printf('<a href="%s%s/%s">', $this->templateEngine->getAdminURL(), "/index.php/post", $key["PrimaryKey"]);
	printf("Title: %s URI: %s Date: %s", $key["Title"], $key["URI"], date('m-d-y', strtotime($key["Date"])));
	echo '</a>';
	echo '</div>';
}
?>
</div>
<?php require_once("footer.php"); ?>