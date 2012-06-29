<?php require_once("header.php"); ?>
<form name="deletePages" class="well" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">

<table class="table">
	<thead>
		<th>Delete</th>
		<th>Page Info</th>
	</thead>
	<tbody>
<?php 
foreach($this->templateEngine->getTheData() as $key)
{
	echo '<tr>';
		echo '<td>';
		//echo '<input name="pageDelete[]" type="checkbox" value="' . $key["PrimaryKey"] . '">';
		printf('<input name="pageDelete[]" type="checkbox" value="%s">', $key["PrimaryKey"]);
		echo '</td>';
		echo '<td>';
		//echo '<a href="./page/'.$key["PrimaryKey"].'">';
		printf('<a href="./page/%s">', $key["PrimaryKey"]);
		//echo "Title: " . $key["Title"] . " " . "URI: " . $key["URI"] . " " . "Date: " . date('m-d-y', strtotime($key["Date"]));
		printf('Title: %s URI: %s Date: %s', $key["Title"], $key["URI"], date('m-d-y', strtotime($key["Date"])));
		echo '</a>';
		echo '</td>';
	echo '</tr>';
}

//echo "<br><br> The array of all the data<br>";
//print_r($this->templateEngine->getTheData());
?>
	</tbody>
</table>

		<input name="type" type="hidden" value="pageRemove">
		<input name="submit" type="submit" value="Delete" class="btn btn-danger">
</form>

<?php
echo $this->templateEngine->havePreviousPagesPageHtml();
if($this->templateEngine->havePreviousPagesPageHtml() !== "" && $this->templateEngine->haveNextPagesPageHtml() !== "")
{
	echo " | ";
}
echo $this->templateEngine->haveNextPagesPageHtml();
?>
<?php require_once("footer.php"); ?>