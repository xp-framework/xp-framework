<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('remote.Remote', 'util.cmd.ParamString');
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    Console::writeLine(<<<__
ESDL demo application

Usage
-----
$ php reflect.php <hostname> [-p <port>] [-b <jndiname>]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * port is the port the ESDL-MBean is listening on. It defaults to 6449.
  
  * jndiname is the name of the bean. If this parameter is omitted,
    all deployed beans are listed.
__
    );
    exit(1);
  }
  
  try(); {
    $remote= &Remote::forName('xp://'.$p->value(1).':'.$p->value('port', 'p', 6449).'/');
    $remote && $services= &$remote->lookup('Services');
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  if ($p->exists('bean')) {
    Console::writeLine(xp::stringOf($services->bean($p->value('bean'))));
  } else {
    foreach ($services->beans() as $description) {
      Console::writeLine(xp::stringOf($description));
    }
  }
  // }}}
?>
