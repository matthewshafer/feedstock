<?php require_once("header.php"); ?>
<div id="postBox">
<?php 
echo "Post's containing the tag ";
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