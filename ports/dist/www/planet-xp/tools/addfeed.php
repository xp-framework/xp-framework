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
  
  if ($param->count < 1 || !$param->exists('url', 'u')) {
    Console::writeLinef('%s --url=<url> [--syndicate=<n>]', $param->value(0));
    Console::writeLine('  --url           URL of RSS/RDF feed');
    Console::writeLine('  --syndicate     Id of the syndicate (default 1)');
    exit(-1);
  }
  
  $syndicate= ($param->exists('syndicate') ? $param->value('syndicate') : 1);
  
  try(); {
    $db= &$cm->getByHost('syndicate', 0);
    
    $res= $db->insert('
      feed (
        url
      ) values (
        %s
      )',
      $param->value('url')
    );
    
    $feed_id= $db->identity();
    
    $res= $db->insert('
      syndicate_feed_matrix (
        syndicate_id,
        feed_id
      ) values (
        %d,
        %d
      )',
      $syndicate,
      $feed_id
    );
  } if (catch('SQLException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine('New feed_id: '.$feed_id);
?>
