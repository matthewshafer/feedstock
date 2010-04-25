<?php require_once("header.php"); ?>
<div id="snippitBox">

<?php

foreach($this->templateEngine->getTheData() as $key)
{
	echo '<div class="singleCorralBox">';
	if(isset($key["PrimaryKey"]))
	{
		//echo '<a href="../page/'.$key["PrimaryKey"].'">';
		printf('<a href="../snippet/%s">%s</a>', $key["PrimaryKey"], $key["Name"]);
	}
	echo '</div>';
}
?>

</div>
<?php require_once("footer.php"); ?>