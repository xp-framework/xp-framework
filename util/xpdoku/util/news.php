<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses(
    'xml.rdf.RDFNewsFeed',
    'util.Properties',
    'rdbms.ConnectionManager',
    'net.xp_framework.db.caffeine.XPNews'
  );
  
  // {{{ main
  
  // Set up connection manager
  $cm= &ConnectionManager::getInstance();
  $cm->configure(new Properties(dirname(__FILE__).'/connection.ini'));
  
  $rdf= &new RDFNewsFeed();
  $rdf->setChannel(
    'XP News', 
    'http://xp-framework.net/',
    'XP Newsflash',
    NULL,
    'en_US',
    'XP-Team <xp@php3.de>',
    'XP-Team <xp@php3.de>',
    'http://xp-framework.net/copyright.html'
  );
  
  // Get news from database
  try(); {
    $news= &XPNews::getByDateOrdered();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  for ($i= 0, $s= sizeof($news); $i < $s; $i++) {
    $rdf->addItem(
      $news[$i]->getCaption(),
      $news[$i]->getLink(),
      $news[$i]->getBody(),
      $news[$i]->getCreated_at()
    );
  }
  
  // Dump
  echo $rdf->getSource($indent= FALSE);
  exit(0);
  // }}}
?>
