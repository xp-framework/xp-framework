<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.profiling.Timer', 'util.Binford');
  
  // {{{ IfElseStrategy
  //     if / else if combination
  class IfElseStrategy extends Object {
  
    function run($arg, $times) {
      for ($i= 0; $i < $times; $i++) {
        if (is_int($arg)) {
          $r= 'INTEGER';
        } else if (is_string($arg)) {
          $r= 'STRING';
        } else if (is_bool($arg)) {
          $r= 'BOOL';
        } else if ($arg instanceof Object) {
          $r= 'OBJECT';
        }
      }
      return $r;
    }
  }
  // }}}

  // {{{ TernaryStrategy
  //     Ternary operators.
  class TernaryStrategy extends Object {
  
    function run($arg, $times) {
      for ($i= 0; $i < $times; $i++) {
        $r= is_int($arg)
          ? 'INTEGER'
          : (is_string($arg)
            ? 'STRING'
            : (is_bool($arg)
              ? 'BOOL'
              : ($arg instanceof Object
                ? 'OBJECT'
                : NULL
              )
            )
          )
        ;
      }
      return $r;
    }
  }
  // }}}

  // {{{ BreakStrategy
  //     do / while (0) loop w/ break
  class BreakStrategy extends Object {
  
    function run($arg, $times) {
      for ($i= 0; $i < $times; $i++) {
        do {
          if (is_int($arg) && $r= 'INTEGER') break;
          if (is_string($arg) && $r= 'STRING') break;
          if (is_bool($arg) && $r= 'BOOL') break;
          if ($arg instanceof Object && $r= 'OBJECT') break;
        } while(0);
      }
      return $r;
    }
  }
  // }}}

  // {{{ SwitchStrategy
  //     switch/case statement
  class SwitchStrategy extends Object {
  
    function run($arg, $times) {
      for ($i= 0; $i < $times; $i++) {
        switch (gettype($arg)) {
          case 'integer': $r= 'INTEGER'; break;
          case 'string': $r= 'STRING'; break;
          case 'boolean': $r= 'BOOL'; break;
          case $arg instanceof Object: $r= 'OBJECT'; break;
        }
      }
      return $r;
    }
  }
  // }}}

  // {{{ main
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', '?') || !$p->exists(1)) {
    Console::writeLinef(<<<__
Tests performance of switch/case vs. if/else vs. ternary vs. do/while/break

Usage:
$ php switch.php <strategy> [-t times]
  
  * strategy is one of "switch", "case", "ternary" or "break"
  
  * times specifies how often to call the function and defaults to 100.000
__
    );
    exit(1);
  }
  
  $strategy= XPClass::forName(ucfirst($p->value(1)).'Strategy')->newInstance();
  $times= $p->value('times', 't', 100000);
  $t= new Timer();

  foreach (array('More power', -1, FALSE, new Binford(6100)) as $arg) {
    $t->start(); {
      $r= $strategy->run($arg, $times);
    } $t->stop();

    Console::writeLinef(
      '%s<%s:%s>: %.3f seconds for %d calls', 
      $strategy->getClassName(), 
      xp::stringOf($arg),
      $r,
      $t->elapsedTime(), 
      $times
    );
  }
  // }}}
?>
