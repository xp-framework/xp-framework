<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.profiling.unittest.TestSuite', 'util.Properties');
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    printf("Usage: %s configuration\n", $p->value(0));
    exit(-1);
  }
  
  $config= &new Properties($p->value(1));
  $suite= &new TestSuite();
  
  $section= $config->getFirstSection();
  do {
    try(); {
      $class= &XPClass::forName($config->readString($section, 'class'));
    } if (catch('ClassNotFoundException', $e)) {
      Console::write('*** Error: Test group "'.$section.'" ~ ', $e->toString());
      exit(-2);
    }
    
    for ($methods= $class->getMethods(), $i= 0, $s= sizeof($methods); $i < $s; $i++) {
      $name= $methods[$i]->getName();
      
      // Ignore non-test and non-public methods
      if (!$methods[$i]->hasAnnotation('test')) continue;
      if ($methods[$i]->hasAnnotation('ignore')) {
        Console::writeLinef(
          '*** Ignoring %s (%s)', 
          $name,
          $methods[$i]->getAnnotation('ignore')
        );
        continue;
      }
      
      // Add test method
      $arguments= array_merge($name, $config->readArray($section, 'args'));
      $suite->addTest(call_user_func_array(
        array(&$class, 'newInstance'), 
        $arguments
      ));
    }
  } while ($section= $config->getNextSection());
  
  // Run test suite
  $result= &$suite->run();
  Console::writeLine($result->toString());
  // }}}
?>
