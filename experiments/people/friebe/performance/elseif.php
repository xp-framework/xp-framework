<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.profiling.Timer');

  // {{{ ElseIfStrategy
  class ElseIfStrategy extends Object {
  
    function run($times) {
      $two= $three= $four= $else= 0;
      for ($i= 0; $i < $times; $i++) {
        if ($i % 2 == 0) {
          $two++;
        } elseif ($i % 3 == 0) {
          $three++;
        } elseif ($i % 4 == 0) {
          $four++;
        } else {
          $else++;
        }
      }
    }
  }
  // }}}

  // {{{ ElseStrategy
  class ElseStrategy extends Object {
  
    function run($times) {
      $two= $three= $four= $else= 0;
      for ($i= 0; $i < $times; $i++) {
        if ($i % 2 == 0) {
          $two++;
        } else if ($i % 3 == 0) {
          $three++;
        } else if ($i % 4 == 0) {
          $four++;
        } else {
          $else++;
        }
      }
    }
  }
  // }}}

  // {{{ SwitchStrategy
  class SwitchStrategy extends Object {
  
    function run($times) {
      $two= $three= $four= $else= 0;
      for ($i= 0; $i < $times; $i++) {
        switch (TRUE) {
          case $i % 2 == 0: $two++; break;
          case $i % 3 == 0: $three++; break;
          case $i % 4 == 0: $four++; break;
          default: $else++; break;
        }
      }
    }
  }
  // }}}

  // {{{ main
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', '?') || !$p->exists(1)) {
    Console::writeLinef(<<<__
Tests performance of switch vs. elseif vs. else if.

Usage:
$ php elseif.php <strategy> [-t times]
  
  * strategy is one of "elseif", "else", "switch"
  
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
