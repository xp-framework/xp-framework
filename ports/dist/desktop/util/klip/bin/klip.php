<?php
/* This file is part of the klip port
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('gui.gtk');
  uses(
    'util.Properties',
    'de.thekid.gui.gtk.Klip'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    Console::writeLinef('Usage: %s <ini_file>', $p->value(0));
    exit(-1);
  }
  
  $prop= &new Properties($p->value(1));
  try(); {
    $prop->reset();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
 
  run(new Klip($p, $prop));
  // }}}
?>
