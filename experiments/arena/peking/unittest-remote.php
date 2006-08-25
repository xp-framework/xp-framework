<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('remote.Remote');

  // {{{ main
  $p= &new ParamString();
  try(); {
    $r= &Remote::forName('xp://'.$p->value(1));
    $r && $bean= &$r->lookup('xp/test/TestRunner');
    $bean && $results= $bean->runTestClass($p->value(2));
  } if (catch('Exception', $e)) {
    Console::writeLine('*** ', $p->value(2).'@'.$p->value(1), ' ~ ', $e->toString());
    exit(-1);
  }
  
  Console::writeLine(xp::stringOf($results));
  // }}}
?>
