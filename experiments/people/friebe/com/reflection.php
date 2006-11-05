<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('ActiveXObject');
  
  // {{{ main
  try(); {
    $object= &new ActiveXObject($argv[1]);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  Console::writeLine($object->toString());
  // }}}
?>
