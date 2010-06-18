<?php require_once("header.php"); ?>
			<form name="createPost" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">
				Post Title: (We create a URI based off this)<br>
				<input name="postTitle" type="text" class="textInput" value="<?php echo $this->templateEngine->postTitleId(); ?>"><br>
				Post Body: <br>
				<textarea name="postorpagedata"><?php echo $this->templateEngine->postBodyId(); ?></textarea>
				<br>
				Categories: (This is going to be a formatted list where you check boxes.)<br>
				<!-- <input name="postCategories" type="text" value="<?php echo $this->templateEngine->postCategoriesId(); ?>"><br> -->
				<?php foreach($this->templateEngine->getCategoryData() as $key)
				{
					if(isset($key["Checked"]) and $key["Checked"] == 1)
					{
						echo '<input name="postCategories[]" type="checkbox" value="' . $key["PrimaryKey"] . '" checked>' . $key["Name"] . '<br>' . "\n\t\t\t\t";
					}
					else
					{
						echo '<input name="postCategories[]" type="checkbox" value="' . $key["PrimaryKey"] . '">' . $key["Name"] . '<br>' . "\n\t\t\t\t";
					}
				} 
				echo "\n";
				?>
				Tags: (separate with a ,)<br>
				<input name="postTags" type="text" class="textInput" value="<?php echo $this->templateEngine->postTagsId(); ?>"><br>
				Draft: <br>
				yes<input name="draft" type="radio" value="1" <?php echo $this->templateEngine->isDraft() == 1 ? "Checked" : ""; ?>>
				no<input name="draft" type="radio" value="0" <?php echo $this->templateEngine->isDraft() == 0 ? "Checked" : ""; ?>>
				<input name="type" type="hidden" value="postAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->postId(); ?>">
				<br>
				<input name="submit" type=submit value="Post" class="button">
			</form>
<?php require_once("footer.php"); ?>