<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Create Post</title>
	</head>
	
	<body>
		<div id="center">
			<form name="createPost" method="post" action="<?php echo V_URL . V_HTTPBASE;?>admin/index.php">
				Post Title: (We create a URI based off this)<br>
				<input name="postTitle" type="text" value="<?php echo $this->templateEngine->postTitleID(); ?>"><br>
				Post Body: <br>
				<textarea name="postorpagedata"><?php echo $this->templateEngine->postBodyID(); ?></textarea>
				<br>
				Categories: (This is going to be a formatted list where you check boxes.)<br>
				<!-- <input name="postCategories" type="text" value="<?php echo $this->templateEngine->postCategoriesID(); ?>"><br> -->
				
				<?php foreach($this->templateEngine->getCategoryData() as $key)
				{
				echo '<input name="postCategories[]" type="checkbox" value="' . $key["PrimaryKey"] . '">' . $key["Name"] . '<br>' . "\n\t\t\t\t";
				} 
				echo "\n";
				?>
				Tags: (separate with a ,)<br>
				<input name="postTags" type="text" value="<?php echo $this->templateEngine->postTagsID(); ?>"><br>
				Draft: <br>
				yes<input name="draft" type="radio" value="1" checked>
				no<input name="draft" type="radio" value="0">
				<input name="type" type="hidden" value="postAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->postID(); ?>">
				<br>
				<input name="submit" type=submit value="Post">
			</form>
		</div>
	</body>
</html>