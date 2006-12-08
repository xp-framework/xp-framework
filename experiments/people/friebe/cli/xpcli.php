<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');

  // {{{ main
  $p= &new ParamString();
  try(); {
    $class= &XPClass::forName($p->value(1));
  } if (catch('ClassNotFoundException', $e)) {
    return throw($e);
  }
  
  // Usage
  if ($p->exists('help', '?')) {
    foreach ($class->getMethods() as $method) {
      if (!$method->hasAnnotation('arg')) continue;
      
      $arg= $method->getAnnotation('arg');
      $name= strtolower(preg_replace('/^set/', '', $method->getName()));;
      $comment= trim($method->getComment());
      list($first, )= $method->getArguments();
      if (isset($arg['position'])) {
        $details['#'.($arg['position'] + 1)]= $comment;
        $positional[$arg['position']]= $name;
      } else if (isset($arg['name'])) {
        $details['--'.$arg['name'].' | -'.(isset($arg['short']) ? $arg['short'] : $arg['name']{0})]= $comment;
        $extra[$arg['name']]= $first->isOptional();
      } else {
        $details['--'.$name.' | -'.(isset($arg['short']) ? $arg['short'] : $name{0})]= $comment;
        $extra[$name]= $first->isOptional();
      }
    }
    
    // Usage
    asort($positional);
    Console::write('Usage: $ xpcli ', $class->getName(), ' ');
    foreach ($positional as $name) {
      Console::write('<', $name, '> ');
    }
    foreach ($extra as $name => $optional) {
      Console::write(($optional ? '[' : ''), '--', $name, ($optional ? '] ' : ' '));
    }
    Console::writeLine();
    
    // Argument details
    Console::writeLine('Arguments:');
    foreach ($details as $which => $comment) {
      Console::writeLine('* ', $which, "\n  ", $comment, "\n");
    }
    exit(1);
  }
  
  $instance= &$class->newInstance();
  foreach ($class->getMethods() as $method) {
    if (!$method->hasAnnotation('arg')) continue;
    
    if (0 == $method->numArguments()) {
      Console::writeLine('*** Method ', $method->toString(), ' does not accept any arguments');
      exit(1);
    }
    
    $arg= $method->getAnnotation('arg');
    if (isset($arg['position'])) {
      $name= '#'.$arg['position'];
      $select= intval($arg['position'])+ 2;
      $short= NULL;
    } else if (isset($arg['name'])) {
      $name= $select= $arg['name'];
      $short= isset($arg['short']) ? $arg['short'] : NULL;
    } else {
      $name= $select= strtolower(preg_replace('/^set/', '', $method->getName()));
      $short= isset($arg['short']) ? $arg['short'] : NULL;
    }

    if (!$p->exists($select, $short)) {
      list($first, )= $method->getArguments();
      if (!$first->isOptional()) {
        Console::writeLine('*** Argument '.$name.' does not exist!');
        exit(-1);
      }
      
      $args= array();
    } else {
      $args= array($p->value($select, $short));
    }
    
    try(); {
      $method->invoke($instance, $args);
    } if (catch('Throwable', $e)) {
      Console::writeLine('*** Error for argument '.$name.': '.$e->getMessage());
      exit(-2);
    }
  }
  
  $instance->run();
  // }}}
?>
