<?php require_once("header.php"); ?>
			<form name="createSnippet" method="post" action="<?php echo V_URL . V_HTTPBASE;?>admin/index.php">
				Snippet Title:<br>
				<input name="snippetTitle" type="text" value="<?php echo $this->templateEngine->snippetTitleID(); ?>"><br>
				Post Body: <br>
				<textarea name="postorpagedata"><?php echo $this->templateEngine->snippetBodyID(); ?></textarea>
				<br>
				<input name="type" type="hidden" value="snippetAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->snippetID(); ?>">
				<br>
				<input name="submit" type=submit value="Submit">
			</form>
<?php require_once("footer.php"); ?>