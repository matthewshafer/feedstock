<form name="deletePages" method="post" action="<?php echo V_URL . V_HTTPBASE;?>admin/index.php">

<div id="pageBox">
<?php 
foreach($this->templateEngine->getTheData() as $key)
{
	echo '<div class="singlePageBox">';
	echo '<input name="pageDelete[]" type="checkbox" value="' . $key["PrimaryKey"] . '">';
	echo '<a href="./page/'.$key["PrimaryKey"].'">';
	echo "Title: " . $key["Title"] . " " . "URI: " . $key["URI"] . " " . "Date: " . date('m-d-y', strtotime($key["Date"]));
	echo '</a>';
	echo '</div>';
}

//echo "<br><br> The array of all the data<br>";
//print_r($this->templateEngine->getTheData());
?>
</div>

		<input name="type" type="hidden" value="pageRemove">
		<input name="submit" type=submit value="Delete">
</form>