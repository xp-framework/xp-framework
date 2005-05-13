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
  $quiet= $param->exists('quiet', 'q');
  
  // Load all feeds that require updates
  try(); {
    $db= &$cm->getByHost('syndicate', 0);
    
    $feeds= &$db->select('
        feed_id,
        url,
        lastcheck
      from
        syndicate.feed
      where (nextcheck < now()
        or (nextcheck is NULL and checkinterval is NULL)
        or now() < date_add(lastcheck, interval checkinterval second))
        and bz_id <= 20000
        %c',
      ($param->exists('feed') ? $db->prepare('and feed_id= %d', $param->value('feed')) : '')
    );
  } if (catch('SQLException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  foreach ($feeds as $feed) {
    try(); {
      $quiet || Console::writeLinef('===> Aggregating feed [%d] from %s', $feed['feed_id'], $feed['url']);
      $controller= &new AggregateController(
        $feed['feed_id'], 
        $feed['url'], 
        $param->exists('full-sync', 'fs') ? NULL : $feed['lastcheck']
      );
      $res= $controller->fetch();
    } if (catch('Exception', $e)) {
      $e->printStackTrace();
      continue;
    }
    
    try(); {
      $res && $controller->update();
    } if (catch('SQLException', $e)) {
      $e->printStackTrace();
      exit(-1);
    }
  }
  
  /// }}}

?>
