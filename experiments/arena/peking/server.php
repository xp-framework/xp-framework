<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'peer.server.PreforkingServer',
    'remote.server.deploy.Deployer',
    'remote.server.deploy.scan.FileSystemScanner',
    'remote.server.ContainerManager',
    'remote.server.EascProtocol',
    'remote.server.ScannerThread'
  );
  
  // Set up loggin
  $log= &Logger::getInstance();
  $cat= &$log->getCategory();
  $cat->addAppender(new ColoredConsoleAppender());
  
  // {{{ main
  declare(ticks= 1);

  $thread= &new ScannerThread(new FileSystemScanner(dirname(__FILE__).'/deploy'));
  $thread->setTrace($cat);
  $thread->setScanPeriod(5);
  
  with ($server= &new PreforkingServer($argv[1], 6448)); {
    $server->setProtocol(new EascProtocol(
      new FileSystemScanner(dirname(__FILE__).'/deploy')
    ));
    
    $server->setTcpNodelay(TRUE);
    $server->setTrace($cat);


    $cat->info('Starting server');
    $server->init();
    $thread->start();
    $server->service();
    $thread->stop();
    $server->shutdown();
  }
  // }}}
?>
