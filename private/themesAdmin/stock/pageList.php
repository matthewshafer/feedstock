<?php require_once("header.php"); ?>
<form name="deletePages" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">

<div id="pageBox">
<?php 
foreach($this->templateEngine->getTheData() as $key)
{
	echo '<div class="singlePageBox">';
	//echo '<input name="pageDelete[]" type="checkbox" value="' . $key["PrimaryKey"] . '">';
	printf('<input name="pageDelete[]" type="checkbox" value="%s">', $key["PrimaryKey"]);
	//echo '<a href="./page/'.$key["PrimaryKey"].'">';
	printf('<a href="./page/%s">', $key["PrimaryKey"]);
	//echo "Title: " . $key["Title"] . " " . "URI: " . $key["URI"] . " " . "Date: " . date('m-d-y', strtotime($key["Date"]));
	printf('Title: %s URI: %s Date: %s', $key["Title"], $key["URI"], date('m-d-y', strtotime($key["Date"])));
	echo '</a>';
	echo '</div>';
}

//echo "<br><br> The array of all the data<br>";
//print_r($this->templateEngine->getTheData());
?>
</div>

		<input name="type" type="hidden" value="pageRemove">
		<input name="submit" type=submit value="Delete" class="button">
</form>
<?php require_once("footer.php"); ?>