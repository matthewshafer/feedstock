<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Create Page</title>
	</head>
	
	<body>
		<div id="center">
			<form name="createPage" method="post" action="<?php echo V_URL . V_HTTPBASE;?>admin/index.php">
				Page Title: (We create a URI based off this)<br>
				<input name="pageTitle" type="text" value="<?php echo $this->templateEngine->pageTitleID(); ?>"><br>
				Custom URI:<br>
				<input name="pageUri" type="text"><br>
				Post Body: <br>
				<textarea name="postorpagedata"><?php echo $this->templateEngine->pageBodyID(); ?></textarea>
				<br>
				Draft: <br>
				yes<input name="draft" type="radio" value="1" checked>
				no<input name="draft" type="radio" value="0">
				<input name="type" type="hidden" value="pageAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->pageID(); ?>">
				<br>
				<input name="submit" type=submit value="Post">
			</form>
			</form>
		</div>
	</body>
</html>