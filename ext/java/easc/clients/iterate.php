<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'remote.Remote', 
    'util.cmd.ParamString', 
    'util.profiling.Timer',
    'util.profiling.unittest.AssertionFailedError'
  );
  
  // {{{ main
  $p= new ParamString();
  if (!$p->exists(1)) {
    Console::writeLine(<<<__
EASC iterator bean demo application. Iterates on $argv of this program.

Usage
-----
$ php iterate.php <hostname> [-p <port> ] [-j <jndi_name>]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * port is the port the XP-MBean is listening on. It defaults to 6448.
  
  * jndi_name is the name of the bean in JNDI. It defaults to 
    "xp/demo/IteratorDemo"
__
    );
    exit(1);
  }
  
  try {
    $remote= Remote::forName('xp://'.$p->value(1).':'.$p->value('port', 'p', 6448).'/');
    $instance= $remote->lookup($p->value('jndi', 'j', 'xp/demo/IteratorDemo'))->create();
  } catch (Throwable $e) {
    $e->printStackTrace();
    exit(-1);
  }

  $iterator= $instance->iterateOn(new ArrayList($argv));
  while ($iterator->hasNext()) {
    Console::writeLine('* ', xp::stringOf($iterator->next()));
  }
  // }}}
?>
