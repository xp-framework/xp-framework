 Database bugs  running on ci.schlund.de

# phpMyAdmin MySQL-Dump
# version 2.5.0-rc1
# http://www.phpmyadmin.net/ (download page)
#
# Host: ci.schlund.de
# Generation Time: Sep 29, 2003 at 12:29 PM
# Server version: 3.23.49
# PHP Version: 4.3.1
# Database : `bugs`
# --------------------------------------------------------

#
# Table structure for table `attachments`
#
# Creation: Jan 19, 2003 at 03:48 PM
# Last update: Sep 29, 2003 at 11:29 AM
# Last check: Aug 08, 2003 at 12:28 PM
#

CREATE TABLE `attachments` (
  `attach_id` mediumint(9) NOT NULL auto_increment,
  `bug_id` mediumint(9) NOT NULL default '0',
  `creation_ts` timestamp(14) NOT NULL,
  `description` mediumtext NOT NULL,
  `mimetype` mediumtext NOT NULL,
  `ispatch` tinyint(4) default NULL,
  `filename` mediumtext NOT NULL,
  `thedata` longblob NOT NULL,
  `submitter_id` mediumint(9) NOT NULL default '0',
  `isobsolete` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`attach_id`),
  KEY `bug_id` (`bug_id`),
  KEY `creation_ts` (`creation_ts`)
) TYPE=MyISAM AUTO_INCREMENT=2214 ;
# --------------------------------------------------------

#
# Table structure for table `attachstatusdefs`
#
# Creation: Jan 19, 2003 at 03:49 PM
# Last update: Jan 19, 2003 at 03:49 PM
#

CREATE TABLE `attachstatusdefs` (
  `id` smallint(6) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `description` mediumtext,
  `sortkey` smallint(6) NOT NULL default '0',
  `product` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `attachstatuses`
#
# Creation: Jan 19, 2003 at 03:49 PM
# Last update: Jan 19, 2003 at 03:49 PM
#

CREATE TABLE `attachstatuses` (
  `attach_id` mediumint(9) NOT NULL default '0',
  `statusid` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`attach_id`,`statusid`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `bugs`
#
# Creation: Sep 09, 2003 at 03:33 PM
# Last update: Sep 29, 2003 at 12:22 PM
# Last check: Sep 09, 2003 at 03:33 PM
#

CREATE TABLE `bugs` (
  `bug_id` mediumint(9) NOT NULL auto_increment,
  `groupset` bigint(20) NOT NULL default '0',
  `assigned_to` mediumint(9) NOT NULL default '0',
  `bug_file_loc` text,
  `bug_severity` enum('blocker','critical','major','normal','minor','trivial','enhancement') NOT NULL default 'blocker',
  `bug_status` enum('UNCONFIRMED','NEW','ASSIGNED','REOPENED','RESOLVED','VERIFIED','CLOSED') NOT NULL default 'UNCONFIRMED',
  `creation_ts` datetime NOT NULL default '0000-00-00 00:00:00',
  `delta_ts` timestamp(14) NOT NULL,
  `short_desc` mediumtext,
  `op_sys` enum('All','Windows 3.1','Windows 95','Windows 98','Windows ME','Windows 2000','Windows NT','Windows XP','Mac System 7','Mac System 7.5','Mac System 7.6.1','Mac System 8.0','Mac System 8.5','Mac System 8.6','Mac System 9.x','MacOS X','Linux','BSDI','FreeBSD','NetBSD','OpenBSD','AIX','BeOS','HP-UX','IRIX','Neutrino','OpenVMS','OS/2','OSF/1','Solaris','SunOS','other') NOT NULL default 'All',
  `priority` enum('P1','P2','P3','P4','P5') NOT NULL default 'P1',
  `product` varchar(64) NOT NULL default '',
  `rep_platform` enum('All','DEC','HP','Macintosh','PC','SGI','Sun','Other') default NULL,
  `reporter` mediumint(9) NOT NULL default '0',
  `version` varchar(64) NOT NULL default '',
  `component` varchar(50) NOT NULL default '',
  `resolution` enum('','FIXED','INVALID','WONTFIX','LATER','REMIND','DUPLICATE','WORKSFORME','MOVED') NOT NULL default '',
  `target_milestone` varchar(20) NOT NULL default '---',
  `qa_contact` mediumint(9) NOT NULL default '0',
  `status_whiteboard` mediumtext NOT NULL,
  `votes` mediumint(9) NOT NULL default '0',
  `keywords` mediumtext NOT NULL,
  `lastdiffed` datetime NOT NULL default '0000-00-00 00:00:00',
  `everconfirmed` tinyint(4) NOT NULL default '0',
  `reporter_accessible` tinyint(4) NOT NULL default '1',
  `cclist_accessible` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`bug_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `creation_ts` (`creation_ts`),
  KEY `delta_ts` (`delta_ts`),
  KEY `bug_severity` (`bug_severity`),
  KEY `bug_status` (`bug_status`),
  KEY `op_sys` (`op_sys`),
  KEY `priority` (`priority`),
  KEY `product` (`product`),
  KEY `reporter` (`reporter`),
  KEY `version` (`version`),
  KEY `component` (`component`),
  KEY `resolution` (`resolution`),
  KEY `votes` (`votes`)
) TYPE=MyISAM AUTO_INCREMENT=6294 ;
# --------------------------------------------------------

#
# Table structure for table `bugs_activity`
#
# Creation: Jan 19, 2003 at 03:49 PM
# Last update: Sep 29, 2003 at 12:22 PM
#

CREATE TABLE `bugs_activity` (
  `bug_id` mediumint(9) NOT NULL default '0',
  `who` mediumint(9) NOT NULL default '0',
  `bug_when` datetime NOT NULL default '0000-00-00 00:00:00',
  `fieldid` mediumint(9) NOT NULL default '0',
  `added` tinytext,
  `removed` tinytext,
  `attach_id` mediumint(9) default NULL,
  KEY `bug_id` (`bug_id`),
  KEY `bug_when` (`bug_when`),
  KEY `fieldid` (`fieldid`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `cc`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 29, 2003 at 11:41 AM
#

CREATE TABLE `cc` (
  `bug_id` mediumint(9) NOT NULL default '0',
  `who` mediumint(9) NOT NULL default '0',
  UNIQUE KEY `bug_id` (`bug_id`,`who`),
  KEY `who` (`who`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `components`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 23, 2003 at 04:54 PM
#

CREATE TABLE `components` (
  `value` tinytext,
  `program` varchar(64) default NULL,
  `initialowner` mediumint(9) NOT NULL default '0',
  `initialqacontact` mediumint(9) NOT NULL default '0',
  `description` mediumtext NOT NULL
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `dependencies`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 23, 2003 at 01:31 PM
#

CREATE TABLE `dependencies` (
  `blocked` mediumint(9) NOT NULL default '0',
  `dependson` mediumint(9) NOT NULL default '0',
  KEY `blocked` (`blocked`),
  KEY `dependson` (`dependson`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `duplicates`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 26, 2003 at 09:24 AM
#

CREATE TABLE `duplicates` (
  `dupe_of` mediumint(9) NOT NULL default '0',
  `dupe` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`dupe`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `fielddefs`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Jan 19, 2003 at 03:58 PM
#

CREATE TABLE `fielddefs` (
  `fieldid` mediumint(9) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `description` mediumtext NOT NULL,
  `mailhead` tinyint(4) NOT NULL default '0',
  `sortkey` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`fieldid`),
  UNIQUE KEY `name` (`name`),
  KEY `sortkey` (`sortkey`)
) TYPE=MyISAM AUTO_INCREMENT=34 ;
# --------------------------------------------------------

#
# Table structure for table `groups`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Jan 19, 2003 at 03:50 PM
#

CREATE TABLE `groups` (
  `bit` bigint(20) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `isbuggroup` tinyint(4) NOT NULL default '0',
  `userregexp` tinytext NOT NULL,
  `isactive` tinyint(4) NOT NULL default '1',
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `bit` (`bit`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `keyworddefs`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Jan 19, 2003 at 03:50 PM
#

CREATE TABLE `keyworddefs` (
  `id` smallint(6) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `description` mediumtext,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `keywords`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Jan 22, 2003 at 11:35 AM
#

CREATE TABLE `keywords` (
  `bug_id` mediumint(9) NOT NULL default '0',
  `keywordid` smallint(6) NOT NULL default '0',
  UNIQUE KEY `bug_id` (`bug_id`,`keywordid`),
  KEY `keywordid` (`keywordid`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `logincookies`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 29, 2003 at 12:22 PM
#

CREATE TABLE `logincookies` (
  `cookie` mediumint(9) NOT NULL auto_increment,
  `userid` mediumint(9) NOT NULL default '0',
  `lastused` timestamp(14) NOT NULL,
  `ipaddr` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`cookie`),
  KEY `lastused` (`lastused`)
) TYPE=MyISAM AUTO_INCREMENT=3442 ;
# --------------------------------------------------------

#
# Table structure for table `longdescs`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 29, 2003 at 12:22 PM
#

CREATE TABLE `longdescs` (
  `bug_id` mediumint(9) NOT NULL default '0',
  `who` mediumint(9) NOT NULL default '0',
  `bug_when` datetime NOT NULL default '0000-00-00 00:00:00',
  `thetext` mediumtext,
  KEY `bug_id` (`bug_id`),
  KEY `who` (`who`),
  KEY `bug_when` (`bug_when`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `milestones`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 23, 2003 at 11:13 AM
#

CREATE TABLE `milestones` (
  `value` varchar(20) NOT NULL default '',
  `product` varchar(64) NOT NULL default '',
  `sortkey` smallint(6) NOT NULL default '0',
  UNIQUE KEY `product` (`product`,`value`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `namedqueries`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 25, 2003 at 01:00 PM
#

CREATE TABLE `namedqueries` (
  `userid` mediumint(9) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `watchfordiffs` tinyint(4) NOT NULL default '0',
  `linkinfooter` tinyint(4) NOT NULL default '0',
  `query` mediumtext NOT NULL,
  UNIQUE KEY `userid` (`userid`,`name`),
  KEY `watchfordiffs` (`watchfordiffs`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `products`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Aug 15, 2003 at 05:15 PM
#

CREATE TABLE `products` (
  `product` varchar(64) default NULL,
  `description` mediumtext,
  `milestoneurl` tinytext NOT NULL,
  `disallownew` tinyint(4) NOT NULL default '0',
  `votesperuser` smallint(6) NOT NULL default '0',
  `maxvotesperbug` smallint(6) NOT NULL default '10000',
  `votestoconfirm` smallint(6) NOT NULL default '0',
  `defaultmilestone` varchar(20) NOT NULL default '---'
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `profiles`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 26, 2003 at 12:30 PM
#

CREATE TABLE `profiles` (
  `userid` mediumint(9) NOT NULL auto_increment,
  `login_name` varchar(255) NOT NULL default '',
  `cryptpassword` varchar(34) default NULL,
  `realname` varchar(255) default NULL,
  `groupset` bigint(20) NOT NULL default '0',
  `disabledtext` mediumtext NOT NULL,
  `mybugslink` tinyint(4) NOT NULL default '1',
  `blessgroupset` bigint(20) NOT NULL default '0',
  `emailflags` mediumtext,
  `person_id` bigint(20) default NULL,
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `login_name` (`login_name`)
) TYPE=MyISAM AUTO_INCREMENT=2128 ;
# --------------------------------------------------------

#
# Table structure for table `profiles_activity`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Jan 19, 2003 at 03:50 PM
#

CREATE TABLE `profiles_activity` (
  `userid` mediumint(9) NOT NULL default '0',
  `who` mediumint(9) NOT NULL default '0',
  `profiles_when` datetime NOT NULL default '0000-00-00 00:00:00',
  `fieldid` mediumint(9) NOT NULL default '0',
  `oldvalue` tinytext,
  `newvalue` tinytext,
  KEY `userid` (`userid`),
  KEY `profiles_when` (`profiles_when`),
  KEY `fieldid` (`fieldid`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `shadowlog`
#
# Creation: Sep 23, 2003 at 06:47 PM
# Last update: Sep 29, 2003 at 12:22 PM
#

CREATE TABLE `shadowlog` (
  `id` int(11) NOT NULL auto_increment,
  `ts` timestamp(14) NOT NULL,
  `reflected` tinyint(4) NOT NULL default '0',
  `command` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `reflected` (`reflected`)
) TYPE=MyISAM AUTO_INCREMENT=7631 ;
# --------------------------------------------------------

#
# Table structure for table `tokens`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 24, 2003 at 04:19 PM
#

CREATE TABLE `tokens` (
  `userid` mediumint(9) NOT NULL default '0',
  `issuedate` datetime NOT NULL default '0000-00-00 00:00:00',
  `token` varchar(16) NOT NULL default '',
  `tokentype` varchar(8) NOT NULL default '',
  `eventdata` tinytext,
  PRIMARY KEY  (`token`),
  KEY `userid` (`userid`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `versions`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 17, 2003 at 02:46 PM
#

CREATE TABLE `versions` (
  `value` tinytext,
  `program` varchar(64) NOT NULL default ''
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `votes`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Jan 19, 2003 at 03:50 PM
#

CREATE TABLE `votes` (
  `who` mediumint(9) NOT NULL default '0',
  `bug_id` mediumint(9) NOT NULL default '0',
  `count` smallint(6) NOT NULL default '0',
  KEY `who` (`who`),
  KEY `bug_id` (`bug_id`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `watch`
#
# Creation: Jan 19, 2003 at 03:50 PM
# Last update: Sep 11, 2003 at 04:44 PM
#

CREATE TABLE `watch` (
  `watcher` mediumint(9) NOT NULL default '0',
  `watched` mediumint(9) NOT NULL default '0',
  UNIQUE KEY `watcher` (`watcher`,`watched`),
  KEY `watched` (`watched`)
) TYPE=MyISAM;

    

