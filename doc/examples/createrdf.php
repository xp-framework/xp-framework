<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'xml.rdf.RDFNewsFeed',
    'util.Properties',
    'rdbms.DriverManager',
    'rdbms.ConnectionManager',
    'net.xp-framework.db.caffeine.XPNews'
  );
  
  $cm= &ConnectionManager::getInstance();
  $cm->register(DriverManager::getConnection(
    'sybase://news:stuemper@php3/CAFFEINE?autoconnect=1'
  ), 'caffeine');
  
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
  
  Console::write($rdf->getSource($indent= INDENT_DEFAULT));
?>
