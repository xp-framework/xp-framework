<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses(
    'xml.rdf.RDFNewsFeed'
  );
  
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
  
  $news->addItem(
    'API Docs released', 
    'http://xp.php3.de/apidoc/',
    'An initial release of the XP api docs has been created'
  );
  
  echo $news->getSource(0);
?>
