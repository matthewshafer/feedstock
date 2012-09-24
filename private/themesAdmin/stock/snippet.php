<?php require_once("header.php"); ?>
<form name="deleteSnippets" class="well" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">

<table class="table">
	<thead>
		<th>Delete</th>
		<th>Snippet Info</th>
	</thead>
	<tbody>

<?php

foreach($this->templateEngine->getTheData() as $key): ?>
	<tr>
		<td>
	<?php
	if(isset($key["PrimaryKey"]))
	{
		//echo '<a href="../page/'.$key["PrimaryKey"].'">';
		printf('<input name="snippetDelete[]" type="checkbox" value="%s">', $key["PrimaryKey"]);
		echo '</td>';
		echo '<td>';
		printf('<a href="../snippet/%s">%s</a>', $key["PrimaryKey"], $key["Name"]);
	}
	?>
		</td>
	</tr>

<?php endforeach; ?>

	</tbody>
</table>

		<input name="type" type="hidden" value="snippetRemove">
		<input name="submit" type="submit" value="Delete" class="btn btn-danger">
</form>
<?php require_once("footer.php"); ?>