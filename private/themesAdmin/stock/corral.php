<?php require_once("header.php"); ?>

			<form name="createCorral" class="well form-inline" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">
				<div class="control-group">
					<label class="control-label"><h4>Corral Name:</h4></label>
				</div>
				<div class="control-group">
					<input name="corralTitle" type="text" placeholder="Name">
					<input name="type" type="hidden" value="corralAdd">
					<input name="submit" type="submit" class="btn btn-primary" value="Post">
				</div>
			</form>

<div class="well">

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