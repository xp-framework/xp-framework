<?php
/*
 * This file is part of the XP framework's ports
 *
 * $Id$
 */
  
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.log.Logger',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'net.planet-xp.aggregator.AggregateController'
  );
  
  /// {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  
  $cm= &ConnectionManager::getInstance();
  $cm->configure($pm->getProperties('database'));
  
  $param= &new ParamString();
  
  try(); {
    $db= &$cm->getByHost('syndicate', 0);

    $feeds= $db->select('
        f.feed_id,
        f.title,
        f.url,
        f.bz_id,
        f.lastcheck,
        (select count(*) from syndicate.feeditem where feed_id= f.feed_id) as cnt
      from  
        syndicate.feed f
    ');
  } if (catch('SQLException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  foreach ($feeds as $feed) {
    Console::writeLinef('[%02d]: %s', $feed['feed_id'], $feed['title']);
    Console::writeLine('      '.$feed['url']);
    Console::writeLinef('      bz_id [%d] articles [%d] lastcheck [%s]',
      $feed['bz_id'],
      $feed['cnt'],
      (is('util.Date', $feed['lastcheck']) ? $feed['lastcheck']->toString() : '')
    );
    Console::writeLine();
    
  }
  
?>
