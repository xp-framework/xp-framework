<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('io.Folder');

  // {{{ main
  $d= new Folder(create(new ParamString())->value(1, NULL, '.'));
  while ($entry= $d->getEntry()) {
    Console::writeLinef('%s%s', $d->getURI(), $entry);
  }
  $d->close();
  // }}}
?>
