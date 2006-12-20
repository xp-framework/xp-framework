<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('peer.ftp.FtpConnection');

  // {{{ main
  $p= new ParamString();
  if (!$p->exists(1)) {
    Console::writeLinef('Usage: %s %s ftp://user:password@host/', $p->value(-1), $p->value(0));
    exit(1);
  }

  try {
    $c= new FtpConnection($p->value(1)); 
    $c->connect();
    $c->setPassive(TRUE); 
    $home= $c->getDir();
  } catch (XPException $e) {
    $e->printStackTrace();
    $c->close();
    exit(-1);
  }
  
  Console::writeLine('Home directory: ', xp::stringOf($home));
  $i= 0; 
  while (($e= $home->getEntry()) && ++$i) { 
    Console::writeLine('+ ', $i, ': ', $e->getClassName(), '(', $e->getName(), ')'); 
  }
  Console::writeLine($i, ' element(s)');
  $c->close();
  // }}}
?>
