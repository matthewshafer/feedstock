<?php require_once("header.php"); ?>
			
			<form name="createCategory" class="well form-inline" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">
				<div class="control-group">
					<label class="control-label"><h4>Category Name: (We create a URI based off this)</h4></label>
				</div>
				<div class="control-group">
					<input name="categoryTitle" type="text" value="<?php echo $this->templateEngine->categoryTitleId(); ?>">
					<input name="type" type="hidden" value="categoryAdd">
					<input name="id" type="hidden" value="<?php echo $this->templateEngine->categoryId(); ?>">
					<input name="submit" type="submit" class="btn btn-primary" value="Post">
				</div>
			</form>
			
			<div class="well">

				<table class="table">
					<thead>
						<th>Delete(NYI)</th>
						<th>Name</th>
						<th>Uri</th>
					</thead>
					<tbody>
			<?php
			foreach($this->templateEngine->getCategoryData() as $key)
			{
				echo '<tr>';
				echo '<td>';
				printf('<input name="categoriesDelete[]" type="checkbox" value="%s">', $key["PrimaryKey"]);
				echo '</td>';

				echo '<td>';
				echo $key["Name"];
				echo '</td>';

				echo '<td>';
				echo $key["URIName"];
				echo '</td>';

				echo '</tr>';
			}

			//echo "<br><br> The array of all the data<br>";
			//print_r($this->templateEngine->getCategoryData());
			?>

				</table>
			</div>
<?php require_once("footer.php"); ?>