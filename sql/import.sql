SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


--
-- Table structure for table `fs_catstags`
--

CREATE TABLE `fs_catstags` (
  `PrimaryKey` int(11) NOT NULL auto_increment,
  `Name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `URIName` varchar(255) collate utf8_unicode_ci NOT NULL,
  `Type` tinyint(4) NOT NULL,
  `SubCat` tinyint(4) NOT NULL default '-1',
  PRIMARY KEY  (`PrimaryKey`),
  KEY `URINameINDEX` (`URIName`),
  KEY `TypeIndex` (`Type`),
  KEY `TypePrimaryIndex` (`Type`,`PrimaryKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fs_pages`
--

CREATE TABLE `fs_pages` (
  `PrimaryKey` int(11) NOT NULL auto_increment,
  `Title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `NiceTitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `URI` varchar(255) collate utf8_unicode_ci NOT NULL,
  `PageData` mediumtext collate utf8_unicode_ci NOT NULL,
  `Author` tinyint(4) NOT NULL,
  `Date` datetime NOT NULL,
  `themeFile` varchar(50) collate utf8_unicode_ci NOT NULL,
  `Draft` tinyint(4) NOT NULL default '0',
  `Corral` varchar(50) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`PrimaryKey`),
  KEY `CorralIndex` (`Corral`),
  KEY `URIDraftINDEX` (`URI`,`Draft`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fs_posts`
--

CREATE TABLE `fs_posts` (
  `PrimaryKey` int(11) NOT NULL auto_increment,
  `Title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `NiceTitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `URI` varchar(255) collate utf8_unicode_ci NOT NULL,
  `PostData` mediumtext collate utf8_unicode_ci NOT NULL,
  `Category` text collate utf8_unicode_ci NOT NULL,
  `Tags` text collate utf8_unicode_ci NOT NULL,
  `Author` tinyint(4) NOT NULL,
  `Date` datetime NOT NULL,
  `themeFile` varchar(50) character set ascii NOT NULL,
  `Draft` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`PrimaryKey`),
  KEY `URI` (`URI`),
  KEY `Date_Draft` (`Draft`,`Date`),
  KEY `DraftPrimaryIndex` (`Draft`,`PrimaryKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fs_posts_tax`
--

CREATE TABLE `fs_posts_tax` (
  `PostID` int(11) NOT NULL,
  `CatTagID` int(11) NOT NULL,
  KEY `PostIDindex` (`PostID`),
  KEY `CatTagIDindex` (`CatTagID`),
  KEY `PostCatTagIndex` (`PostID`,`CatTagID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fs_snippet`
--

CREATE TABLE `fs_snippet` (
  `PrimaryKey` int(11) NOT NULL auto_increment,
  `Name` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `SnippetData` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`PrimaryKey`),
  KEY `NameIndex` (`Name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fs_users`
--

CREATE TABLE `fs_users` (
  `id` int(11) NOT NULL auto_increment,
  `loginName` tinytext NOT NULL,
  `displayName` tinytext NOT NULL,
  `PasswordHash` varchar(512) character set ascii collate ascii_bin NOT NULL,
  `Salt` varchar(255) character set ascii collate ascii_bin NOT NULL,
  `Permissions` tinyint(4) NOT NULL default '99',
  `CanAdminUsers` tinyint(4) NOT NULL default '0',
  `CookieVal` varchar(512) character set ascii collate ascii_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
