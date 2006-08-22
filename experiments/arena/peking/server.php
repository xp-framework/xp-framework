<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'peer.server.Server',
    'remote.server.deploy.Deployer',
    'remote.server.ContainerManager',
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
    
    // Perform deployment
    $cm= &new ContainerManager();
    try(); {
      $deployer= &new Deployer();
      $bc= &$deployer->deployBean(XPClass::forName('net.xp_framework.beans.stateless.RoundtripBean'), $cm);
    } if (catch('Exception', $e)) {
      return throw($e);
    }

    $server->init();
    $server->service();
    $server->shutdown();
  }
  // }}}
?>
