<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('io.Folder');

  // {{{ main
  $p= new ParamString();
  
  $d= new Folder($p->value(1, NULL, '.'));
  while ($entry= $d->getEntry()) {
    Console::writeLinef('%s%s', $d->getURI(), $entry);
  }
  $d->close();
  // }}}
?>
