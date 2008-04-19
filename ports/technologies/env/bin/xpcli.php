<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.cmd.Runner');

  // {{{ main
  exit(Runner::main(array_slice($argv, 1)));
  // }}}
?>
