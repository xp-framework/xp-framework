<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('xml.rdf.RDFNewsFeed');
  
  $news= &new RDFNewsFeed();
  $news->setChannel(
    'XP News', 
    'http://xp.php3.de/',
    'XP Newsflash',
    NULL,
    'en_US',
    'XP-Team <xp@php3.de>',
    'XP-Team <xp@php3.de>',
    'http://xp.php3.de/copyright.html'
  );
  
  // TBD: Get from database?
  $news->addItem(
    'NetClassLoader finalized', 
    'http://xp.php3.de/apidoc/classes/lang.NetClassLoader.html',
    'The ability to load classes via HTTP or HTTPS allows, for example, software to update it self.',
    new Date('2003-02-01 02:57:52')
  );
  $news->addItem(
    'CSVParser class added', 
    'http://xp.php3.de/apidoc/classes/util.text.parser.CSVParser.html',
    'CSVParser provides comfortable way to parse csv (comma separated value) - files.',
    new Date('2003-01-20 13:06:51')
  );
  $news->addItem(
    'MySQL Adapter completed', 
    'http://xp.php3.de/apidoc/classes/rdbms.mysql.html',
    'Reverse engineer MySQL - tables, databases, attributes and keys. This functionality allows for automatic creation of database classes from MySQL.',
    new Date('2003-01-20 12:50:52')
  );
  $news->addItem(
    'About XP', 
    'http://xp.php3.de/content/about.html',
    'An introduction to the XP framework, including installation tips, coding standards, a howto on class documentation and CVS guidelines is now available. Enjoy reading!',
    new Date('2003-01-18 21:00:43')
  );
  $news->addItem(
    '[Beta] Added collection org.webdav', 
    'http://xp.php3.de/apidoc/collections/org.webdav.html',
    'A first version now allows clients to browse a webdav directory',
    new Date('2003-01-05 07:24:58')
  );
  $news->addItem(
    'Added collection util.text.format', 
    'http://xp.php3.de/apidoc/collections/util.text.format.html',
    'Formatting strings has become even easier. Have a look at the MessageFormat class for examples',
    new Date('2003-01-04 22:02:00')
  );
  $news->addItem(
    '[Beta] Telephony API', 
    'http://xp.php3.de/apidoc/collections/util.telephony.html',
    'With providing an API to telephony applications, another very interesting feature has been introduced',
    new Date('2002-12-30 16:32:00')
  );
  $news->addItem(
    'XP goes "multimedia"', 
    'http://xp.php3.de/apidoc/collections/util.mp3.html',
    'Initial support for MP3 files was added',
    new Date('2002-12-30 14:12:00')
  );
  $news->addItem(
    'Added class org.cvshome.CVSInterface', 
    'http://xp.php3.de/apidoc/collections/org.cvshome.html',
    'Description: This class is an easy to use interface to the concurrent versioning system executables',
    new Date('2002-12-29 19:00:00')
  );
  $news->addItem(
    'API Docs: Inheritance tree', 
    'http://xp.php3.de/apidoc/inheritance.html',
    'A tree view on class inheritance is now available.',
    new Date('2002-12-29 18:43:54')
  );
  $news->addItem(
    'API Docs released', 
    'http://xp.php3.de/apidoc/',
    'An initial release of the XP api docs has been created. There is still some missing functionality but the documentation is already quite usable.',
    new Date('2002-12-28 19:18:12')
  );
  $news->addItem(
    'Website created', 
    'http://xp.php3.de/',
    'The first version of the XP web site is online.',
    new Date('2002-12-27 13:10:01')
  );
  
  echo $news->getSource(0);
?>
