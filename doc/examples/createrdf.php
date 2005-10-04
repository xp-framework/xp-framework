<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'xml.rdf.RDFNewsFeed',
    'util.log.Logger',
    'util.log.LogObserver',
    'util.log.ConsoleAppender',
    'util.Properties',
    'rdbms.DriverManager',
    'rdbms.ConnectionManager',
    'net.xp_framework.db.caffeine.XPNews'
  );
  
  // {{{ main
  $p= &new ParamString();
  
  // Set up connection manager
  $cm= &ConnectionManager::getInstance();
  $conn= &$cm->register(DriverManager::getConnection(
    'sybase://'.$p->value(1).'/CAFFEINE?autoconnect=1'
  ), 'caffeine');
  
  // Enable debugging if requested
  if ($p->exists('debug')) {
    $l= &Logger::getInstance();
    $cat= &$l->getCategory('sql');
    $cat->addAppender(new ConsoleAppender());
    $conn->addObserver(LogObserver::instanceFor('sql'));
  }

  // Create RDF object
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
  
  // Get news entries ordered by date, maximum 20
  try(); {
    $news= &XPNews::getByDateOrdered(20);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Add news entries to RDF
  for ($i= 0, $s= sizeof($news); $i < $s; $i++) {
    $rdf->addItem(
      $news[$i]->getCaption(),
      $news[$i]->getLink(),
      $news[$i]->getBody(),
      $news[$i]->getCreated_at()
    );
  }
  
  // Output XML
  Console::write($rdf->getSource($indent= INDENT_DEFAULT));
  // }}}
?>
