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
EASC entity bean demo application

Usage
-----
$ php feed.php <hostname> [<command> [<arguments>]] [-p <port> ] [-j <jndi_name> ]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * command is one of the following:
    - list    Lists all fields (default if not supplied)
    - get     Gets a feed by a supplied feed id

  * port is the port the XP-MBean is listening on. It defaults to 6448.
  
  * jndi_name is the name of the bean in JNDI. It defaults to 
    "xp/planet/Feed"
__
    );
    exit(1);
  }
  
  try(); {
    $remote= &Remote::forName('xp://'.$p->value(1).':'.$p->value('port', 'p', 6448).'/');
    $remote && $home= &$remote->lookup($p->value('jndi', 'j', 'xp/planet/Feed'));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  switch ($p->value(2, NULL, 'list')) {
    case 'get':
      $feed= &$home->findByPrimaryKey(new Long($p->value(3)));
      Console::writeLine(xp::stringOf($feed->getFeedValue()));
      break;
    
    case 'list':
    default:
      foreach ($home->findAll() as $i => $feed) {
        Console::writeLinef('%3d] %s', $i, xp::stringOf($feed->getFeedValue()));
      }
      break;
  }
  // }}}
?>
