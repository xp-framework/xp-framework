<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses('ActiveXObject');
  
  try(); {
    $object= &new ActiveXObject($argv[1]);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  Console::writeLine($object->toString());
?>
