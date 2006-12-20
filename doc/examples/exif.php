<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('img.util.ExifData', 'io.File');
  
  // {{{ main
  $p= new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLinef(
      'Usage: %s %s <infile>',
      $p->value(-1),
      $p->value(0)
    );
    exit(1);
  }
  
  // Load original
  Console::write('===> Loading ', $p->value(1), ': ');
  try {
    $data= ExifData::fromFile(new File($p->value(1)));
  } catch (ImagingException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  Console::writeLine($data->toString());
  // }}}
?>
