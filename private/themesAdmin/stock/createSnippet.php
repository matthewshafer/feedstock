<?php require_once("header.php"); ?>
			<form name="createSnippet" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">
				Snippet Title:<br>
				<input name="snippetTitle" type="text" class="textInput" value="<?php echo $this->templateEngine->snippetTitleId(); ?>"><br>
				Post Body: <br>
				<textarea name="postorpagedata"><?php echo $this->templateEngine->snippetBodyId(); ?></textarea>
				<br>
				<input name="type" type="hidden" value="snippetAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->snippetId(); ?>">
				<br>
				<input name="submit" type="submit" value="Submit" class="button">
			</form>
<?php require_once("footer.php"); ?>