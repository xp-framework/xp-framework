<?php
/* This file is part of the XP framework's people experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  
  // {{{ proto void implements(string interface [, string interface [, ...]]) 
  //     Defines that the class this is called in implements certain interface(s)
  function implements() {
    $t= debug_backtrace();
    $class= substr(basename($t[0]['file']), 0, -10);
    $classmethods= get_class_methods($class);
    
    foreach (func_get_args() as $interface) {
      uses($interface);    
      foreach (get_class_methods($interface) as $method) {
        if (!in_array($method, $classmethods)) {
          $e= new Error(
            'Interface method '.$interface.'::'.$method.'() not implemented by class '.$class
          );
          $e->printStackTrace();
          exit(0x7f);
        }
      }

      $implements= xp::registry('implements');
      $implements[strtolower($class)][strtolower($interface)]= 1;
      xp::registry('implements', $implements);
    }
  }
  // }}}
  
  // {{{ proto bool is(string class, &lang.Object object)
  //     Checks whether a given object is of the class, a subclass or implements an interface
  function is($class, &$object) {
    if (is_a($object, $class)) return TRUE;
    $implements= xp::registry('implements');
    
    if ($p= get_class($object)) do {
      if (isset($implements[$p][strtolower($class)])) return TRUE;
    } while ($p= get_parent_class($p));
  }
  // }}}
  
  // {{{ main
  $p= &new ParamString();
  try(); {
    $class= &XPClass::forName($p->value(1));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLinef(
    '---> is("Iterator", new %1$s()) = %2$d', 
    $p->value(1),
    is('Iterator', $class->newInstance()
  ));
  // }}}
?>
