<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('io.dba.DBAFile');
  
  // {{{ main
  $p= new ParamString();

  $dba= new DBAFile($p->value(1), $p->value(2, NULL, DBH_DB4));
  $dba->open(DBO_READ);
  
  // Use the iterator functionality
  for ($i= $dba->iterator(); $i->hasNext(); ) {
    $key= $i->next();
    Console::writeLinef('[%-14s] %s', $key, var_export($dba->fetch($key), 1));
  }
  
  // Close and clean up
  $dba->close();
  // }}}
?>
