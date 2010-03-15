<?php

$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!@#$%^&*()_+=-|}{[];:,./<>?\'\\';

$runFor = 125;
$len = strlen($str) - 1;
$randomized = null;

for($i = 0; $i < $runFor; $i++)
{
	$randomized .= $str[rand(0, $len)];
}

echo htmlentities($randomized);

if(isset($_POST["username"]) and isset($_POST["password"]))
{
	$this->db->addUser($_POST["username"], $_POST["password"], $randomized);
}

?>