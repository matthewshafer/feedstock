<?php
/* General Site Information */
define("V_SITETITLE", "Feedstock Test");
define("V_DESCRIPTION", "Just a little test!");
define("V_THEME", "stopwatch");
// Are we using HTACCESS or rewrite rules?
define("V_HTACCESS", false);
// Web address and base location
define("V_URL", "http://localhost:8888/");
define("V_HTTPBASE", "niftysvn/feedstock/public/");


/* Post Settings */
define("V_POSTFORMAT", "%MONTH%/%DAY%/%YEAR%/%TITLE%");


/* Feed Settings */
define("F_AUTHOR", "username");
define("F_AUTHOREMAIL", "some@email.com");


/* File Download Settings */
// Are we using build in file downloading?
define("F_FILEDOWNLOAD", true);
// file download speed in KB/s
define("V_FILEDOWNLOADSPEED", 200);


/* Database Information */
// database type
define("V_DATABASE", "mysql");
// database connection info
$username = "root";
$password = "root";
$address = "localhost";
$database = "feedstock";
$tableprefix = "fs_";


/* Caching Settings */
define("V_CACHE", false);
define("F_EXPIRECACHETIME", 1);

/* Cookie Settings */
define("F_COOKIENAME", "FeedStock");

/* Password Settings */
define("F_PSALT", "ChangeThisToSomething");

/* Admin Settings */
define("F_ADMINBASE", V_HTTPBASE . "admin/");
define("F_ADMINHTACCESS", false);

/* Other Configs.  You probably don't need to change these */
define("V_BASELOC", dirname(__FILE__));

// error reporting, we want it on for testing
ini_set('display_errors',1); 
error_reporting(E_ALL);


?>