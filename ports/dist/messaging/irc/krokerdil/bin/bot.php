<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'peer.irc.IRCConnection', 
    'util.log.Logger',
    'util.log.FileAppender',
    'de.thekid.irc.KrokerdilBotListener'
  );
  
  // {{{ main
  $p= &new ParamString();
  $c= &new IRCConnection(new IRCUser('KrokerdilBot'), 'irc.moep.net');
  
  if ($p->exists('debug')) {
    $l= &Logger::getInstance();
    $cat= &$l->getCategory();
    $cat->addAppender(new FileAppender('php://stderr'));
    $c->setTrace($cat);
  }
  
  $c->addListener(new KrokerdilBotListener());
  try(); {
    $c->open();
    $c->run();
    $c->close();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
  }
  // }}}
?>
