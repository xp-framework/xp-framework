<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.profiling.unittest.TestSuite',
    'util.Properties'
  );
  
  // {{{ proto void addTest(&util.profiling.unittest.TestSuite suite, &lang.XPClass class [, array arguments])
  //     Adds a test
  function addTest(&$suite, &$class, $arguments= array()) {
  
    // Sanity check
    if (!$class->isSubclassOf('util.profiling.unittest.TestCase')) {
      Console::writeLine('*** Error: ', $class->getName(), ' is not a TestCase');
      exit(-3);
    }

    // Iterate over methods, adding all method annotated with @test (but not @ignore)
    for ($methods= $class->getMethods(), $i= 0, $s= sizeof($methods); $i < $s; $i++) {
      if (!$methods[$i]->hasAnnotation('test')) continue;
      
      if ($methods[$i]->hasAnnotation('ignore')) {
        Console::writeLinef(
          '     >> Ignoring %s::%s (%s)', 
          $class->getName(TRUE), 
          $methods[$i]->getName(),
          $methods[$i]->getAnnotation('ignore')
        );
        continue;
      }
      
      // Add test method
      $suite->addTest(call_user_func_array(array(&$class, 'newInstance'), array_merge(
        (array)$methods[$i]->getName(TRUE),
        $arguments
      )));
    }

    // Print warning and exit if no test are found
    if (0 == $suite->numTests()) {
      Console::writeLine('*** Warning: No tests found in ', $class->getName());
      exit(-4);
    }
  }
  // }}}
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLinef(<<<__
Console-based test runner for XP unit tests

Usage: 
  1) Load test from property-file
     \$ %1\$s %2\$s configuration.ini [<section>]

     The configuration file is expected to have the following format:
     
     -- Sample ------------------------------------------------------
     [dict]
     class="net.xp_framework.unittest.peer.DictTest"
     args="test.dict.org|2628"

     [ldap]
     class="net.xp_framework.unittest.peer.LDAPTest"
     ----------------------------------------------------------------
     
     The args line is optional.


  2) Specify a class to run:
     \$ %1\$s %2\$s --class|-c classname [--arguments|-a arguments]
     
     The class name is expected to extend the base class for all test
     cases, util.profiling.unittest.TestCase.
     
     Arguments is comma-separated, e.g. "test.dict.org,2628"

__
,
      $p->value(-1),
      $p->value(0)
    );
    exit(-1);
  }

  $tests= array();
  if ($p->exists('class')) {        // Class based  
    $name= $p->value('class');
    Console::writeLinef('===> Using class %s', $name);

    $tests[]= array($name, explode(',', $p->value('arguments', 'a', '')));
  } else {                          // Property-file based
    $config= &new Properties($p->value(1));

    Console::writeLinef('===> Using configuration from %s', $p->value(1));
    if ($p->exists(2)) {
      $section= $p->value(2);
      Console::writeLinef('---> Adding tests from section %s', $section);
      
      $tests[]= array(
        $config->readString($section, 'class'),
        $config->readArray($section, 'args')
      );
    } else {
      Console::writeLinef('---> Adding all tests');
      $section= $config->getFirstSection();
      do {
        $tests[]= array(
          $config->readString($section, 'class'),
          $config->readArray($section, 'args')
        );
      } while ($section= $config->getNextSection());
    }
  }
  
  Console::writeLine('===> Setting up suite');
  $suite= &new TestSuite();
  foreach ($tests as $test) {
    try(); {
      $class= &XPClass::forName($test[0]);
    } if (catch('ClassNotFoundException', $e)) {
      Console::write('*** Error: Class "'.$test[0].'" ~ ', $e->toString());
      exit(-2);
    }
    
    addTest($suite, $class, $test[1]);
  }

  Console::writeLine('===> Running test suite');
  $result= &$suite->run();
  Console::write($result->toString());
  // }}}
?>
