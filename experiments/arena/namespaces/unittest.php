<?php
/* This file is part of the XP framework
 *
 * $Id: run.php 9116 2007-01-04 13:51:16Z friebe $ 
 */
  require('lang.base.php');
  uses(
    'util.cmd.ParamString',
    'util.cmd.Console',
    'unittest.TestSuite',
    'util.Properties'
  );
  
  // {{{ proto void addTestClass(unittest.TestSuite suite, lang.XPClass class [, array arguments])
  //     Adds a test
  function addTestClass($suite, $class, $arguments= array()) {
    try {
      $ignored= $suite->addTestClass($class, $arguments);
    } catch (util::NoSuchElementException $e) {
      util::cmd::Console::writeLine('*** Warning: ', $e->getMessage());
      exit(-4);
    } catch (lang::IllegalArgumentException $e) {
      util::cmd::Console::writeLine('*** Error: ', $e->getMessage());
      exit(-3);
    }
    
    foreach ($ignored as $method) {
      util::cmd::Console::writeLinef(
        '     >> Ignoring %s::%s (%s)', 
        $class->getName(TRUE), 
        $method->getName(),
        $method->getAnnotation('ignore')
      );
    }
  }
  // }}}
  
  // {{{ main
  $p= new util::cmd::ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    util::cmd::Console::writeLinef(<<<__
Console-based test runner for XP unit tests

Usage: 
  1) Load test from property-file
     \$ %1\$s %2\$s configuration.ini [<section>]

     The configuration file is expected to have the following format:
     
     -- Sample ------------------------------------------------------
     [this]
     description="Peer integration tests"

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
     cases, unittest.TestCase.
     
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
    util::cmd::Console::writeLinef('===> Using class %s', $name);

    $tests[]= array($name, explode(',', $p->value('arguments', 'a', '')));
  } else {                          // Property-file based
    $config= new util::Properties($p->value(1));

    util::cmd::Console::writeLinef('===> Using configuration from %s', $p->value(1));
    if ($p->exists(2)) {
      $section= $p->value(2);
      util::cmd::Console::writeLinef('---> Adding tests from section %s', $section);
      
      $tests[]= array(
        $config->readString($section, 'class'),
        $config->readArray($section, 'args')
      );
    } else {
      util::cmd::Console::writeLine('---> Adding "', $config->readString('this', 'description'), '"');
      $section= $config->getFirstSection();
      do {
        if ('this' == $section) continue;   // Ignore special section

        $tests[]= array(
          $config->readString($section, 'class'),
          $config->readArray($section, 'args')
        );
      } while ($section= $config->getNextSection());
    }
  }
  
  util::cmd::Console::writeLine('===> Setting up suite');
  $suite= new unittest::TestSuite();
  foreach ($tests as $test) {
    addTestClass($suite, lang::XPClass::forName($test[0]), $test[1]);
  }

  util::cmd::Console::writeLine('===> Running test suite');
  $result= $suite->run();
  util::cmd::Console::write($result->toString());
  // }}}
?>
