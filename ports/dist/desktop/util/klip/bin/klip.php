<?php
/* This file is part of the klip port
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('gui.gtk');
  uses('de.thekid.gui.gtk.Klip');
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    Console::writeLinef('Usage: %s <url_to_klip_file>', $p->value(0));
    exit(-1);
  }
  
  run(new Klip($p));
  // }}}
?>
