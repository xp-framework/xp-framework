<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('FDF', 'io.File');
  
  // {{{ main
  $fdf= &new FDF();
  $fdf->setReferenceUri('demo1.pdf');
  $fdf->setValue('date', date('Y-m-d'));
  $fdf->setValue('time', date('H:i:s').' ('.date('Z').')');
  
  $out= &new File('datetime.fdf');
  Console::writeLine('---> Writing ', $fdf->toString());

  try(); {
    $bytes= $fdf->saveTo($out);
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine('===> Wrote ', $bytes, ' bytes to ', $out->getURI());
  // }}}
?>
