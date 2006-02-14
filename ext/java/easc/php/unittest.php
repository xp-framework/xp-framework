<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.profiling.unittest.TestSuite');
  
  // {{{ main
  $suite= &new TestSuite();

  try(); {
    $class= &XPClass::forName($argv[1]);
  } if (catch('ClassNotFoundException', $e)) {
    Console::write($argv[1], ': ', $e->toString());
    exit(-2);
  }
  
  foreach ($class->getMethods() as $method) {
    if (
      !$method->hasAnnotation('test') ||
      $method->hasAnnotation('ignore')
    ) continue;

    $suite->addTest($class->newInstance($method->getName(TRUE)));
  }
  
  Console::writeLine(xp::stringOf($suite->run()));
  // }}}
?>
