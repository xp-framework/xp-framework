<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('remote.Remote', 'util.cmd.ParamString');

  // {{{ main
  $p= new ParamString();
  if (!$p->exists(1)) {
    Console::writeLine(<<<__
EASC message sender demo application

Usage
-----
$ php message.php <hostname> [-p <port> ] [-j <jndi_name> ] [-q <queue_name>] [-t <text>]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The MessageSender bean (from the easc/beans directory) 
    is expected to be deployed.

  * port is the port the XP-MBean is listening on. It defaults to 6448.
  
  * jndi_name is the name of the bean in JNDI. It defaults to 
    "xp/demo/MessageSender"
  
  * queue_name is the name of the queue the message is being sent to
  
  * text is the text of the message to be sent
__
    );
    exit(1);
  }
  
  try {
    $remote= Remote::forName('xp://'.$p->value(1).':'.$p->value('port', 'p', 6448).'/');
    $sender= $remote->lookup($p->value('jndi', 'j', 'xp/demo/MessageSender'))->create();
  } catch (Throwable $e) {
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
