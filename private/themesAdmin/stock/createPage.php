<?php require_once("header.php"); ?>
			<form name="createPage" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminURL(), '/index.php'); ?>">
				Page Title: (We create a URI based off this)<br>
				<input name="pageTitle" type="text" class="textInput" value="<?php echo $this->templateEngine->pageTitleID(); ?>"><br>
				Custom URI:<br>
				<input name="pageUri" type="text" class="textInput" value="<?php echo $this->templateEngine->pageURI(); ?>"><br>
				Post Body: <br>
				<textarea name="postorpagedata"><?php echo $this->templateEngine->pageBodyID(); ?></textarea>
				<br>
				Corral:<br>
				<input name="pageCorral" type="text" class="textInput" value="<?php echo $this->templateEngine->pageCorral(); ?>"><br>
				<br>
				Draft: <br>
				yes<input name="draft" type="radio" value="1" <?php echo $this->templateEngine->isDraft() == 1 ? "Checked" : ""; ?>>
				no<input name="draft" type="radio" value="0" <?php echo $this->templateEngine->isDraft() == 0 ? "Checked" : ""; ?>>
				<input name="type" type="hidden" value="pageAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->pageID(); ?>">
				<br>
				<input name="submit" type=submit value="Post" class="button">
			</form>
			</form>
<?php require_once("footer.php"); ?>