<?php
/* This file is part of the XP framework's peoples' experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  
  // {{{ main
  $cp= xp::registry('classpath');
  array_unshift($cp, dirname(__FILE__).DIRECTORY_SEPARATOR.'collections.cca');
  xp::registry('classpath', $cp);
  
  uses('util.collections.HashTable');
  
  Console::writeLine('===> ', xp::stringOf(new HashTable()));
  // }}}
?>
