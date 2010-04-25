<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Create Snippit</title>
	</head>
	
	<body>
		<div id="center">
			<form name="createSnippit" method="post" action="<?php echo V_URL . V_HTTPBASE;?>admin/index.php">
				Snippit Title:<br>
				<input name="snippitTitle" type="text" value="<?php echo $this->templateEngine->snippitTitleID(); ?>"><br>
				Post Body: <br>
				<textarea name="postorpagedata"><?php echo $this->templateEngine->snippitBodyID(); ?></textarea>
				<br>
				<input name="type" type="hidden" value="pageAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->snippitID(); ?>">
				<br>
				<input name="submit" type=submit value="Submit">
			</form>
			</form>
		</div>
	</body>
</html>