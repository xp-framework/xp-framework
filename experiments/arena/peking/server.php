<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'peer.server.Server',
    'remote.server.deploy.Deployer',
    'remote.server.deploy.scan.FileSystemScanner',
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
    $scanner= &new FileSystemScanner(dirname(__FILE__).'/deploy');
    $cat->info('Deploying all beans from', $scanner);
    $cm= &new ContainerManager();
    $deployer= &new Deployer();
    foreach ($scanner->getDeployments() as $deployment) {
      if (is('IncompleteDeployment', $deployment)) {
        $cat->warn('Failed:', $deployment);
        continue;
      }

      $cat->debug('Deploying', $deployment);
      $deployer->deployBean($deployment->class, $cm);
    }

    $cat->info('Starting server');
    $server->init();
    $server->service();
    $server->shutdown();
  }
  // }}}
?>
