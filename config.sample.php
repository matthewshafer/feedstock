<?php
/* General Site Information */
define("V_SITETITLE", "Feedstock");
define("V_DESCRIPTION", "Just a little test!");
define("V_THEME", "stopwatch");
// Are we using HTACCESS or rewrite rules?
define("V_HTACCESS", false);
// Web address and base location
define("V_URL", "http://someAddressHere.whatever/");
define("V_HTTPBASE", "/"); // if its not at the base of the address you need to point where it is, could be blog/ for instance
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
define("V_DATABASE", "Mysqli");
// database connection info
$username = "username";
$password = "password";
$address = "localhost";
$database = "databaseName";
$tableprefix = "fs_";


/* Caching Settings */
define("V_CACHE", false);
// name of the cacher.  filecache does standard files.  xcache uses php xcache for caching
define("F_CACHENAME", "ApcDynamic");
// This is in seconds. Should set to something like 1400.  Currently set low to help with testing.
define("F_EXPIRECACHETIME", 120);
// Xcache Setting
define("F_XCACHEPREFIX", "something_");


/* Sitemap Settings */
define("F_SITEMAPGENERATE", false);
define("F_SITEMAPMAXITEMS", 30000);
define("F_PUBLICPATH", "/public/");


/* Cookie Settings */
define("F_COOKIENAME", "FeedStock");


/* Password Settings */
define("F_PSALT", "ChangeThisToSomething");


/* Admin Settings */
define("F_ADMINADDRESS", "http://someAddressHere.whatever/admin/"); // need to point this to the location of the admin page, as you can change it from admin/
define("F_ADMINBASE", V_HTTPBASE . "admin/");
define("F_ADMINHTACCESS", false);


/* Other Configs.  You probably don't need to change these */
define("V_BASELOC", dirname(__FILE__));
setlocale(LC_ALL, 'en_US.UTF8');


// Error reporting, we want it on for testing
ini_set('display_errors',1); // you should comment these out when you go into production as you probably dont want to show errors.  I use it for testing
error_reporting(E_ALL);

// Mysql debugging stuff
define("F_MYSQLSTOREQUERIES", true); // really turn this off when you go into production

?>