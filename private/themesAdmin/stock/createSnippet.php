<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Create Snippet</title>
	</head>
	
	<body>
		<div id="center">
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
			</form>
		</div>
	</body>
</html>