<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id: calculator.php 6205 2005-12-02 12:34:53Z friebe $ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('remote.Remote', 'util.cmd.ParamString');

  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    Console::writeLine(<<<__
EASC message sender demo application

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
    $remote && $home= &$remote->lookup($p->value('jndi', 'j', 'xp/demo/MessageSender'));
    $home && $sender= &$home->create();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Send a text message
  $text= $p->value('text', 't', 'This is a text message for EASC');
  Console::writeLine('===> Sending message text:');
  Console::writeLine($text);
  Console::writeLine(xp::stringOf($sender->sendTextMessage(
    $p->value('queue', 'q', 'queue/MessageQueue'),
    $text
  )));
  // }}}
?>
