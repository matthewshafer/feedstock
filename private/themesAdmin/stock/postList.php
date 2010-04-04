<form name="deletePosts" method="post" action="<?php echo V_URL . V_HTTPBASE;?>admin/index.php">

<?php 
foreach($this->templateEngine->getTheData() as $key)
{
	echo '<input name="postsDelete[]" type="checkbox" value="' . $key["PrimaryKey"] . '">';
	echo "Title: " . $key["Title"] . " " . "URI: " . $key["URI"] . " " . "Date: " . date('m-d-y', strtotime($key["Date"]))  . "<br><br>";
}

//echo "<br><br> The array of all the data<br>";
//print_r($this->templateEngine->getTheData());
?>

		<input name="type" type="hidden" value="postsRemove">
		<input name="submit" type=submit value="Delete">
</form>