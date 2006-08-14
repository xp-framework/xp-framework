<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'peer.server.Server',
    'remote.server.EascProtocol'
  );
  
  // Set up loggin
  $log= &Logger::getInstance();
  $cat= &$log->getCategory();
  $cat->addAppender(new ColoredConsoleAppender());
  
  // {{{ main
  with ($server= &new Server($argv[1], 6448)); {
    $server->setProtocol(new EascProtocol());
    $server->setTcpNodelay(TRUE);
    $server->init();
    $server->service();
    $server->shutdown();
  }
  // }}}
?>
