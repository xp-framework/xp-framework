<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('io.Folder');

  $p= new ParamString();
  try {
    $d= new Folder($p->value(1, NULL, '.'));
    while ($entry= $d->getEntry()) {
      Console::writeLinef('%s%s', $d->getURI(), $entry);
    }
    $d->close();
  } catch (IOException $e) {
    $e->printStackTrace();
    exit(-1);
  }
?>
