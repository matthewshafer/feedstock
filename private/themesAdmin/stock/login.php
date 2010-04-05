<?php require_once("header.php"); ?>
		<div class="centerLogin">
			<form name="login" method="post" action="<?php echo V_URL . V_HTTPBASE;?>admin/index.php">
				Username: <br>
				<input name="username" type="text">
				<br>
				Password: <br>
				<input name="password" type="password">
				<input name="type" type="hidden" value="login">
				<br>
				<input name="submit" type=submit value="Login">
			</form>
		</div>
<?php require_once("footer.php"); ?>