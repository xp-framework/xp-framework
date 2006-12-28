<?php
/* This file provides the class sapi for the XP framework
 * 
 * $Id$
 */
  uses('util.cmd.ParamString');
  
  // {{{ void runnable(void)
  //     Syntax: class Test extends Object { ... } runnable();
  function runnable() {
    $p= new ParamString();
    $class= xp::reflect(basename($p->value(0), '.class.php'));

    $target= array($class, 'main');
    if (!is_callable($target)) {
      xp::error('Target '.$class.'::main() is not runnable');
      // Bails out
    }

    xp::$registry['class.'.$class]= 'Runnable$'.$class;
    exit(call_user_func($target, $p));
  }
  // }}}
?>
