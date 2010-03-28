<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Create Category</title>
	</head>
	
	<body>
		<div id="center">
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

			echo "<br><br> The array of all the data<br>";
			print_r($this->templateEngine->getCategoryData());
			?>
		</div>
	</body>
</html>