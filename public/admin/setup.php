<?php
// for now just point this to the root
$loc = "../../";

require_once($loc . "config.php");
require_once($loc . "private/includes/" . V_DATABASE . "Admin.php");

$db = new databaseAdmin($username, $password, $address, $database, $tableprefix);

$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!@#$%^&*()_+=-|}{[];:,./<>?\'\\';

$runFor = 255;
$len = strlen($str) - 1;
$randomized = null;

for($i = 0; $i < $runFor; $i++)
{
	$randomized .= $str[rand(0, $len)];
}

//echo htmlentities($randomized);

echo '
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Login</title>
	</head>
	
	<body>
		<div id="center">
			<form name="login" method="post" action="./setup.php">
				Username: <br>
				<input name="username" type="text">
				<br>
				Display Name: <br>
				<input name="displayname" type="text">
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

if(isset($_POST["username"]) and isset($_POST["password"]) and isset($_POST["displayname"]))
{
	$db->addUser($_POST["username"], $_POST["displayname"], makePasswordHash($_POST["password"], $randomized), $randomized, 0, 1);
}


function makePasswordHash($p, $s)
{	
		// create some var's we need for later
		$s2 = F_PSALT;
		$preSalt = null;
		$s2len = strlen($s2);
		$slen = strlen($s);
		$start = 0;
		
		// figure out which string is longer
		if($s2len < $slen)
		{
			$length = $slen;
		}
		else
		{
			$length = $s2len;
		}
		
		// mix up the two salt's into one new salt
		while($start < $length)
		{
			if($start < $slen)
			{
				$preSalt .= $s[$start];
			}
			
			if($start < $s2len)
			{
				$preSalt .= $s2[$start];
			}
			
			$start++;
		}
		
		// split up the password into two parts
		$password = str_split($p, (strlen($p)/2)+1);
		
		// same deal with the salt
		$salt = str_split($preSalt, (strlen($preSalt)/2)+1);
		
		// hash them using whirlpool with the salts added
		$hash = hash('whirlpool', $password[0].$salt[0].$password[1].$salt[1]);
		
		return $hash;
	}

?>