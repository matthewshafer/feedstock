<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<Link href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/css/style.css'); ?>" rel="stylesheet" type="text/css">
		<title>Login</title>
	</head>
	
	<body>
		<div class="centerLogin">
			<form name="login" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">
				Username: <br>
				<input name="username" type="text">
				<br>
				Password: <br>
				<input name="password" type="password">
				<input name="type" type="hidden" value="login">
				<br>
				<input name="submit" type="submit" value="Login">
			</form>
		</div>
	</div>
	
	</body>
</html>