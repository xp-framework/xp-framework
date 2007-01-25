<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'peer.server.PreforkingServer',
    'remote.server.deploy.Deployer',
    'remote.server.deploy.scan.FileSystemScanner',
    'remote.server.deploy.scan.SharedMemoryScanner',
    'remote.server.ContainerManager',
    'remote.server.EascProtocol',
    'remote.server.ScannerThread'
  );
  
  $p= new ParamString();
  
  // Set up loggin
  $log= Logger::getInstance();
  $cat= $log->getCategory();
  $cat->addAppender(new ColoredConsoleAppender());
  
  // {{{ main
  declare(ticks= 1);
  
  // Preforking or normal server ? 
  if ($p->exists('fork', 'f')) {
    $thread= new ScannerThread(new FileSystemScanner(dirname(__FILE__).'/deploy'));
    $thread->setTrace($cat);
    $thread->setScanPeriod(5);

    $server= new PreforkingServer($p->value(1, NULL, 'localhost'), $p->value(2, NULL, 6448));
    $server->setProtocol(new EascProtocol(
      new SharedMemoryScanner()
    ));
  } else {
    $server= new Server($p->value(1, NULL, 'localhost'), $p->value(2, NULL, 6448));
    $server->setProtocol(new EascProtocol(
      new FileSystemScanner(dirname(__FILE__).'/deploy')
    )); 
  }
  
  $server->setTcpNodelay(TRUE);
  $p->exists('fork', 'f') && $server->setTrace($cat);

  // Main loop
  $cat->info('Starting server');
  $server->init();

  $p->exists('fork', 'f') && $thread->start();
  $server->service();

  $p->exists('fork', 'f') && $thread->stop(SIGTERM);
  $server->shutdown();
  // }}}
?>
