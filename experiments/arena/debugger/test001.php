<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('Debugger');

  $d= &new Debugger();
  $d->start(); 
  declare(ticks = 1);

  $p= &new ParamString();
  if (!$p->exists(1)) {
    Console::writeLinef('Usage: %s <classname>', $p->value(0));
    exit(1);
  }
  
  try(); {
    $class= &XPClass::forName($p->value(1));
  } if (catch('ClassNotFoundException', $e)) {
    $e->printStackTrace();
    exit(127);
  }

  $i= &$class->newInstance();
  Console::writeLine($i->toString());
  
  $d->stop();
?>
