<?php
/* This file is part of the XP framework
 *
 * $Id $ 
 */

  require('lang.base.php');
  uses('lang.System', 'util.cmd.ParamString');
  
  // {{{ main
  $p= &new ParamString();

  chdir(dirname($p->value(1)));
  try(); {
    $response= System::exec(
      sprintf(
        'cvs diff -u %s',
        basename($p->value(1)
    )));
  } if (catch('Exception', $e)) {
    echo $e->toString();
    exit(-1);
  }
  
  foreach ($response as $string) echo $string."\n";
  // }}}
?>
