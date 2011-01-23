<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
	// change the require to actually point to feedstockAdmin.php
	require_once("../../private/FeedstockAdmin.php");

	$feedstockAdmin = new FeedstockAdmin();

?>