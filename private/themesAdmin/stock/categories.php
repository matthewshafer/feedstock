<?php require_once("header.php"); ?>
			<form name="createCategory" method="post" action="<?php echo V_URL . V_HTTPBASE;?>admin/index.php/categories">
				Category Title: (We create a URI based off this)<br>
				<input name="categoryTitle" type="text" value="<?php echo $this->templateEngine->categoryTitleID(); ?>"><br>
				<input name="type" type="hidden" value="categoryAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->categoryID(); ?>">
				<input name="submit" type=submit value="Post">
			</form>
			<br><br>
			<?php
			foreach($this->templateEngine->getCategoryData() as $key)
			{
				echo "Category: " . $key["Name"] . " " . "URI: " . $key["URIName"] . "<br><br>";
			}

			//echo "<br><br> The array of all the data<br>";
			//print_r($this->templateEngine->getCategoryData());
			?>
<?php require_once("footer.php"); ?>