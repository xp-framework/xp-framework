<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses('peer.server.ForkingServer', 'StorageListener');

  // {{{ main
  $p= &new ParamString();
  $host= $p->exists(1) ? $p->value(1) : '127.0.0.1';
  $port= $p->exists(2) ? (int)$p->value(2) : 6100;
  
  $server= &new ForkingServer($host, $port);
  $server->addListener(new StorageListener());
  try(); {
    $server->init();
    $server->service();
    $server->shutdown();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  // }}}
?>
