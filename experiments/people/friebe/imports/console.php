<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php'); require('imports.php');
  uses('util.cmd.Console');
  imports('util.cmd.Console::writeLine');
  
  // Without imports
  Console::writeLine('Hello');

  // With imports
  writeLine('Hello');
?>
