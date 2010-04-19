<form name="deletePosts" method="post" action="<?php echo V_URL . V_HTTPBASE;?>admin/index.php">

<div id="postBox">
<?php 
foreach($this->templateEngine->getTheData() as $key)
{
	echo '<div class="singlePostBox">';
	//echo '<input name="postsDelete[]" type="checkbox" value="' . $key["PrimaryKey"] . '">';
	printf('<input name="postsDelete[]" type="checkbox" value="%s">', $key["PrimaryKey"]);
	//echo '<a href="./post/'.$key["PrimaryKey"].'">';
	printf('<a href="./post/%s">', $key["PrimaryKey"]);
	//echo "Title: " . $key["Title"] . " " . "URI: " . $key["URI"] . " " . "Date: " . date('m-d-y', strtotime($key["Date"]));
	printf("Title: %s URI: %s Date: %s", $key["Title"], $key["URI"], date('m-d-y', strtotime($key["Date"])));
	echo '</a>';
	echo '</div>';
}

//echo "<br><br> The array of all the data<br>";
//print_r($this->templateEngine->getTheData());
?>
</div>

		<input name="type" type="hidden" value="postsRemove">
		<input name="submit" type=submit value="Delete">
</form>