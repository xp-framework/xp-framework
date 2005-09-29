<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../php/'.PATH_SEPARATOR.'.');
  uses('Remote', 'util.cmd.ParamString');

  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    Console::writeLine(<<<__
EASC calculator demo application

Usage
-----
$ php calculator.php <hostname> [-p <port> ] [-j <jndi_name> ]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The calculator bean (from the easc/beans directory) 
    is expected to be deployed.

  * port is the port the XP-MBean is listening on. It defaults to 6448.
  
  * jndi_name is the name of the bean in JNDI. It defaults to 
    "xp/demo/Calculator"
__
    );
    exit(1);
  }
  
  try(); {
    $remote= &Remote::forName('xp://'.$p->value(1).':'.$p->value('port', 'p', 6448).'/');
    $remote && $home= &$remote->lookup($p->value('jndi', 'j', 'xp/demo/Calculator'));
    $home && $calculator= &$home->create();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // The add() method
  Console::writeLine('1.0 + 1.0 = ', xp::stringOf($calculator->add(1.0, 1.0)));
  Console::writeLine('1 + 1 = ', xp::stringOf($calculator->add(1, 1)));
  Console::writeLine('(2 + 3i) + (3 + 4i) = ', xp::stringOf($calculator->add(new Complex(2, 3), new Complex(3, 4))));

  // The subtract() method
  Console::writeLine('1.0 - 1.3 = ', xp::stringOf($calculator->subtract(1.0, 1.3)));
  Console::writeLine('1 - 12 = ', xp::stringOf($calculator->subtract(1, 12)));

  // The multiply() method
  Console::writeLine('2.0 * 1.5 = ', xp::stringOf($calculator->multiply(2.0, 1.5)));
  Console::writeLine('2 * 6 = ', xp::stringOf($calculator->multiply(2, 6)));
  // }}}
?>
