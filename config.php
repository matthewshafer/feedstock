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
define("F_ADMINADDRESS", "http://localhost:8888/niftysvn/feedstock/public/admin/");
define("F_MAINTENANCE", false);
define("F_MAINTENANCEPASS", "127.0.0.1");


/* Post Settings */
define("V_POSTFORMAT", "%MONTH%/%DAY%/%YEAR%/%TITLE%");
define("F_POSTSPERPAGE", 10);


/* Feed Settings */
define("F_AUTHOR", "username");
define("F_AUTHOREMAIL", "some@email.com");
define("F_PUBSUBHUBBUB", false);
define("F_PUBSUBHUBBUBPUBLISH", "http://pubsubhubbub.appspot.com/publish");
define("F_PUBSUBHUBBUBSUBSCRIBE", "http://pubsubhubbub.appspot.com/");


/* File Download Settings */
// Are we using built in file downloading?
define("F_FILEDOWNLOAD", true);
// file download speed in KB/s
define("V_FILEDOWNLOADSPEED", 200);


/* Database Information */
// database type
define("V_DATABASE", "mysqli");
// database connection info
$username = "root";
$password = "root";
$address = "localhost";
$database = "feedstock";
$tableprefix = "fs_";


/* Caching Settings */
define("V_CACHE", false);
// name of the cacher.  filecache does standard files.  xcache uses php xcache for caching
define("F_CACHENAME", "xcacheDynamic");
// This is in seconds. Should set to something like 1400.  Currently set low to help with testing.
define("F_EXPIRECACHETIME", 120);
// Xcache Setting
define("F_XCACHEPREFIX", "something_");


/* Cookie Settings */
define("F_COOKIENAME", "FeedStock");


/* Password Settings */
define("F_PSALT", "ChangeThisToSomething");


/* Admin Settings */
define("F_ADMINBASE", V_HTTPBASE . "admin/");
define("F_ADMINHTACCESS", false);


/* Other Configs.  You probably don't need to change these */
define("V_BASELOC", dirname(__FILE__));
setlocale(LC_ALL, 'en_US.UTF8');


// Error reporting, we want it on for testing
ini_set('display_errors',1); 
error_reporting(E_ALL);

// Mysql debugging stuff
define("F_MYSQLSTOREQUERIES", false);

?>