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
$ php feed.php <hostname> [<feed_id>] [-p <port> ] [-j <jndi_name> ]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * feed_id represents the id of the feed to get. If omitted, all feeds
    will be listed

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

  if ($p->exists(2)) {
    $feed= &$home->findByPrimaryKey(new Long($p->value(2)));
    Console::writeLine('Title/URL: ', $feed->getTitle(), ' / ', $feed->getURL());
    Console::writeLine(xp::stringOf($feed->getFeedValue()));
    exit();
  }
  
  foreach ($home->findAll() as $i => $feed) {
    Console::writeLine($i, '] Title/URL: ', $feed->getTitle(), ' / ', $feed->getURL());
  }
  // }}}
?>
