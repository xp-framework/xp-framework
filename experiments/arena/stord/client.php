<?php
  require('lang.base.php');
  uses('peer.Socket', 'util.profiling.Timer');

  // {{{ main
  $t= &new Timer();
  $t->start();
  
  $r= array();
  $socket= &new Socket('127.0.0.1', 6100);
  try(); {
    $socket->connect();
    
    for ($i= 0; $i < 10; $i++) {
      $data= array(
        $i,
        1861822,
        'thekid.de',
        'Timm Friebe',
      );
      $socket->write("ADD history.friebe ".serialize($data)."}\r\n");
      $r[]= $socket->readLine();
    }
    $socket->write("GET history.friebe\r\n");
    $l= $socket->readLine();
    $r[]= array($l, unserialize(substr($l, 4)));
    $socket->write("CLEAR history.friebe\r\n");
    $r[]= $socket->readLine();
    $socket->close();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  $t->stop();
  var_dump($r, xp::registry('errors'));
  printf("Time elapsed: %.3f seconds\n", $t->elapsedTime());
  // }}}
?>
