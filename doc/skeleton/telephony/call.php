<?php
  require('lang.base.php');
  uses(
    'ch.ecma.StliConnection', 
    'peer.Socket', 
    'util.log.Logger',
    'util.log.FileAppender',
    'util.cmd.ParamString'
  );
  
  $p= &new ParamString();
  if (4 != $p->count) {
    printf("Usage: %s server:port <from> <to>\n", basename($p->value(0)));
    exit();
  }
  list($server, $port)= explode(':', $p->value(1));
  
  $l= &Logger::getInstance();
  $cat= &$l->getCategory();
  $cat->addAppender(new FileAppender('php://stderr'));
  
  $c= &new StliConnection(new Socket($server, $port));
  $c->setTrace($cat);
  try(); {
    $c->connect() &&
    $term= &$c->getTerminal($c->getAddress('int:'.$p->value(2)));
  } if (catch('IOException', $e)) {
    printf("---> Could not connect server %s\n", $server);
    $e->printStackTrace();
    exit();
  } if (catch('Exception', $e)) {
    printf("---> Could not acquire terminal %s\n", $p->value(2));
    $e->printStackTrace();
    exit;
  }
  
  try(); {
    $call= &$c->createCall($term, $c->getAddress('ext:'.$p->value(3)));
  } if (catch('Exception', $e)) {
    printf("---> Could not place call to %s\n", $p->value(3));
    $e->printStackTrace();
    $c->releaseTerminal($term);
    exit();
  }
  
  // OK, we have a call
  var_dump($call);
  
  try(); { 
    $c->releaseTerminal($term);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }
  
  printf("Done\n");
?>
