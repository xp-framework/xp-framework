<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');

  // {{{ main
  exit(XPClass::forName($argv[1])->getMethod('main')->invoke(NULL, array(array_slice($argv, 2))));
  // }}}
?>
