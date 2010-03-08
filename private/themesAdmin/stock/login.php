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
			<form name="LOGIN" method="post" action="index.php">
				Username: <br>
				<input name="USERNAME" type="text" id="USERNAME">
				<br>
				Password: <br>
				<input name="PASSWORD" type="text" id="PASSWORD">
				<input name="TYPE" type="hidden" id="TYPE" value="LOGIN">
				<br>
				<input name="SUBMIT" type="button" id="SUBMIT" value="Login">
			</form>
		</div>
	</body>
</html>
';
?>