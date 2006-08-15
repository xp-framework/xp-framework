<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.profiling.Timer');
  
  // {{{ fixture
  function fixture($r1, $r2, $o1= array(1, 2), $o2= NULL, $o3= 'foo') {
    if (
      !assert($r1 == 1) ||
      !assert($r2 == 2) ||
      !assert($o1 == array(1, 2)) ||
      !assert($o2 == NULL) ||
      !assert($o3 == 'foo')
    ) xp::error(xp::stringOf(new Error('fixture() called with incorrect arguments')));
  }
  // }}}
  
  // {{{ PassStrategy
  class PassStrategy extends Object {
  
    function run($times) {
      for ($i= 0; $i < $times; $i++) fixture(1, 2, array(1, 2), NULL, 'foo');
    }
  }
  // }}}

  // {{{ CallStrategy
  class CallStrategy extends Object {
  
    function run($times) {
      for ($i= 0; $i < $times; $i++) fixture(1, 2);
    }
  }
  // }}}

  // {{{ main
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', '?') || !$p->exists(1)) {
    Console::writeLinef(<<<__
Tests performance of default arguments.

Usage:
$ php default-args.php <strategy> [-t times]
  
  * strategy is one of "pass", "call".
  
  * times specifies how often to call the function and defaults to 100.000
__
    );
    exit(1);
  }
  
  try(); {
    $class= &XPClass::forName(ucfirst($p->value(1)).'Strategy');
    $class && $strategy= &$class->newInstance();
  } if (catch('ClassNotFoundException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
 
  $times= $p->value('times', 't', 100000);
  $t= &new Timer();
  $t->start(); {
    $strategy->run($times);
  } $t->stop();
  
  Console::writeLinef(
    '%s: %.3f seconds for %d calls', 
    $strategy->getClassName(), 
    $t->elapsedTime(), 
    $times
  );
  // }}}
?>
