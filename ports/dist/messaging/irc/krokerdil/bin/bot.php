<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'peer.irc.IRCConnection',
    'util.Properties',
    'util.log.Logger',
    'util.log.FileAppender',
    'de.thekid.irc.KrokerdilBotListener'
  );
  
  // {{{ main
  $p= &new ParamString();
  try(); {
    $config= &new Properties($p->value(1));
    $config->reset();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Set up IRC connection
  $c= &new IRCConnection(
    new IRCUser(
      $config->readString('irc', 'nickname', 'KrokerdilBot'),
      $config->readString('irc', 'realname', NULL),
      $config->readString('irc', 'username', NULL),
      $config->readString('irc', 'hostname', 'localhost')
    ), 
    $config->readString('irc', 'server')
  );
  
  // Reset socket timeout to a better value for IRC (this
  // prevents IOExceptions being thrown over and over again)
  $c->sock->setTimeout(120);
  
  // Check if debug is wanted and *where* it's wanted
  if ($p->exists('debug')) {
    $l= &Logger::getInstance();
    $cat= &$l->getCategory();
    $cat->addAppender(new FileAppender('php://stderr'));
    $c->setTrace($cat);
  }
  
  // Connect and run the bot
  $c->addListener(new KrokerdilBotListener($config));
  while (1) {
    try(); {
      $c->open();
      $c->run();
      $c->close();
    } if (catch('Exception', $e)) {
      $e->printStackTrace();
      // Fall through
    }

    // Wait for 10 seconds and then try to reconnect
    sleep(10);
  }
  // }}}
?>
