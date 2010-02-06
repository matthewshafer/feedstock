<?php
// Site Title
define("V_SITETITLE", "Feedstock Test");
define("V_DESCRIPTION", "Just a little test!");
// Theme to use, must be in private/themes/ with this foldername
// all of the script/css/image files for the theme must be in their respective directories in /public
define("V_THEME", "stopwatch");

// if we are using htaccess to rewrite url's or not
define("V_HTACCESS", false);
define("V_HTTPBASE", "niftysvn/feedstock/public/");

define("V_DATABASE", "mysql");

// database information
$username = "root";
$password = "root";
$address = "localhost";
$database = "feedstock";
$tableprefix = "fs_";


// caching.  private/cache must be writeable
define("V_CACHE",false);


// define the root location of versions on the server
define("V_BASELOC", dirname(__FILE__));

define("V_URL", "http://localhost:8888/");

define("V_POSTFORMAT", "%MONTH%/%DAY%/%YEAR%/%TITLE%");

define("V_FILEDOWNLOADSPEED", 20);


?>