NOTE FOR THE ALPHAS:
There is NO upgrade path from them. So from alpha1 to alpha2 there is no upgrade.php that does everything for you.
As far as using these in a production environment, that is totally up to you.

Bugs:
If you encounter bugs you can email me the info, matt @ niftystopwatch.com

Requirements:
PHP >= 5.2
PHP Mcrypt (only required for the admin pages)
MySQL >= 5 (ok possibly MySQL 4 as long as it is supported by mysqli)


How To Install:
Import the sql file into your database, you can find it inside the sql folder in the root of this.
Set up the config file with database info and anything else that is needed. Every section should say what it does so just read through it.
go to yoursite/admin/setup.php and create an account.  After you have created an account you can delete this file (should make sure you are able to login at yoursite/admin/ first).
Login at yoursite/admin/ and post some stuff/make pages