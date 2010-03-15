<?php
echo '
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Login</title>
	</head>
	
	<body>
		<div id="center">
			<form name="login" method="post" action="./index.php">
				Username: <br>
				<input name="username" type="text">
				<br>
				Password: <br>
				<input name="password" type="text">
				<input name="type" type="hidden" value="login">
				<br>
				<input name="submit" type=submit value="Login">
			</form>
		</div>
	</body>
</html>
';
?>