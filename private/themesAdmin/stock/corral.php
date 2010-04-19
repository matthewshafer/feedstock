<?php require_once("header.php"); ?>
<div id="corralBox">

<?php

foreach($this->templateEngine->getTheData() as $key)
{
	echo '<div class="singleCorralBox">';
	if(isset($key["PrimaryKey"]))
	{
		//echo '<a href="../page/'.$key["PrimaryKey"].'">';
		printf('<a href="../page/%s">', $key["PrimaryKey"]);
	}
	else
	{
		//echo '<a href="./corral/'.$key["Corral"].'">';
		printf('<a href="./corral/%s">', $key["Corral"]);
	}
	echo $key["Corral"];
	echo '</a>';
	echo '</div>';
}
?>

</div>
<?php require_once("footer.php"); ?>