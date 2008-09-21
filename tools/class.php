<?php 
  require('lang.base.php');
  xp::sapi('cli');

  exit(XPClass::forName($argv[1])->getMethod('main')->invoke(NULL, array(array_slice($argv, 2)))); 
?>
