<?php require_once("header.php"); ?>
			<form name="createCategory" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">
				Category Title: (We create a URI based off this)<br>
				<input name="categoryTitle" type="text" value="<?php echo $this->templateEngine->categoryTitleId(); ?>"><br>
				<input name="type" type="hidden" value="categoryAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->categoryId(); ?>">
				<input name="submit" type="submit" value="Post">
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