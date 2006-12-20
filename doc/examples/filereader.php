<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('io.File', 'io.FileUtil');
  
  $p= new ParamString();
  try {
    $contents= FileUtil::getContents(new File($p->value(1)));
  } catch (IOException $e) {
    $e->printStackTrace();
    exit(-1);
  }

  Console::writeLinef('+ Read %d bytes:', strlen($contents));
?>
