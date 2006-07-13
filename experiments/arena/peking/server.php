<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'peer.server.Server',
    'remote.server.EascReader',
    'remote.server.ApplicationServerListener'
  );
  
  // Set up loggin
  $log= &Logger::getInstance();
  $cat= &$log->getCategory();
  $cat->addAppender(new ColoredConsoleAppender());
  
  // {{{ main
  with ($server= &new Server($argv[1], 6448)); {
    $server->reader= &new EascReader();
    $server->addListener(new ApplicationServerListener());
    $server->init();
    $server->service();
    $server->shutdown();
  }
  // }}}
?>
