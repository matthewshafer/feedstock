<?php
/* General Site Information */
$this->config['siteTitle'] = "Feedstock";
$this->config['siteDescription'] = "Just a little test!";
$this->config['themeName'] = "stopwatch";
// Are we using HTACCESS or rewrite rules?
$this->config['htaccess'] = false;
// Web address and base location
$this->config['siteUrl'] = "http://localhost/";
$this->config['siteUrlBase'] = "/";
$this->config['enableMaintenance'] = false;
$this->config['maintenancePassthru'] = "127.0.0.1";


/* Post Settings */
$this->config['postFormat'] = "%MONTH%/%DAY%/%YEAR%/%TITLE%";
$this->config['postsPerPage'] = 10;


/* Feed Settings */
$this->config['feedAuthor'] = "username";
$this->config['feedAuthorEmail'] = "some@email.com";
$this->config['feedPubSubHubBub'] = false;
$this->config['feedPubSubHubBubPublishUrl'] = "http://pubsubhubbub.appspot.com/publish";
$this->config['feedPubSubHubBubSubscribe'] = "http://pubsubhubbub.appspot.com/";


/* Database Information */
// database type
$this->config['databaseType'] = "Mysqli";
// database connection info
$this->config['databaseUsername'] = "username";
$this->config['databasePassword'] = "password";
$this->config['databaseAddress'] = "localhost";
$this->config['databasePort'] = 1234;
$this->config['databaseName'] = "feedstock";
$this->config['databaseTablePrefix'] = "fs_";


/* Caching Settings */
$this->config['cacheEnable'] = false;
$this->config['cacheName'] = "FileCache";
$this->config['cachePrefix'] = "something_";
$this->config['cacheType'] = "static";
$this->config['cacheExpireTime'] = 120;


/* Sitemap Settings */
$this->config['generateSitemap'] = false;
$this->config['maxSitemapItems'] = 30000;
$this->config['sitemapPath'] = "/public/";


/* Cookie Settings */
// cleaning up the defines
$this->config['cookieName'] = "FeedStock";


/* Password Settings */
$this->config['passSalt'] = "ChangeThisToSomething";


/* Admin Settings */
$this->config['adminAddress'] = "http://localhost";
$this->config['adminBase'] = $this->config['siteUrlBase'] . "admin/";
$this->config['adminHtaccess'] = false;


/* Other Configs.  You probably don't need to change these */
$this->config['baseLocation'] = dirname(__FILE__);
setlocale(LC_ALL, 'en_US.UTF8');


// Error reporting, we want it on for testing
ini_set('display_errors',1); 
error_reporting(E_ALL);

// Mysql debugging stuff
$this->config['databaseDebug'] = false;

?>