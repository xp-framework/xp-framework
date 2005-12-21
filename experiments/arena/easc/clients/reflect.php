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
$ php reflect.php <hostname> [-p <port> ]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * port is the port the ESDL-MBean is listening on. It defaults to 6449.
  
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

  foreach ($services as $name => $description) {
    Console::writeLine(xp::stringOf($description));
  }
  // }}}
?>
