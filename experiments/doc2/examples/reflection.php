<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');

  // {{{ main
  $p= new ParamString();
  try {
    $class= XPClass::forName($p->value(1));
  } catch (ClassNotFoundException $e) {
    $e->printStackTrace();
    exit(-1);
  } catch (Exception $e) {
    $e->printStackTrace();
    exit(-2);
  }

  $parent= $class->getParentClass();
  
  // Retrieve class methods
  $methods= '';
  for ($i= 0, $m= $class->getMethods(), $s= sizeof($m); $i < $s; $i++) {
    $methods.= '  - '.$m[$i]->getName().'() [declared in '.$m[$i]->getDeclaringClass()->getName()."]\n";
  }

  // Retrieve class fields
  $fields= '';
  for ($i= 0, $f= $class->getFields(), $s= sizeof($f); $i < $s; $i++) {
    $fields.= '  - $'.$f[$i]->getName().' [declared in '.$f[$i]->getDeclaringClass()->getName()."]\n";
  }
  
  // Check whether this class is an interface
  if ($class->isInterface()) {
    Console::writef(
      "Interface '%s' (extends %s)\n".
      "* Methods:\n%s\n".
      "* Has method 'toString': %s\n\n",
      $class->getName(),
      $parent ? $parent->getName() : '(n/a)',
      $methods,
      var_export($class->hasMethod('toString'), 1)
    );
  } else {

    // Retrieve implemented interfaces
    $implements= '';
    for ($c= 0, $i= $class->getInterfaces(), $s= sizeof($i); $c < $s; $c++) {
      $implements.= ', '.$i[$c]->getName();
    }
    
    $constructor= $class->getConstructor();

    // Create a new instance
    $instance= $class->newInstance();
  
    Console::writef(
      "Class '%s' (extends %s) %s\n".
      "* Constructor: %s\n".
      "* Methods:\n%s\n".
      "* Fields:\n%s\n".
      "* Has method 'toString': %s\n\n".
      "* Instance toString() output: %s\n\n",
      $class->getName(),
      $parent ? $parent->getName() : '(n/a)',
      $implements ? 'implements '.substr($implements, 2) : '',
      $constructor 
        ? $constructor->getName().'() [declared in '.$constructor->getDeclaringClass()->getName().']'
        : '(none)'
      ,
      $methods,
      $fields,
      var_export($class->hasMethod('toString'), 1),
      $instance->toString()
    );
  
    if ($p->exists('invoke')) {
      with ($m= $class->getMethod($p->value('invoke'))); {
        $result= $m->invoke($instance);
        Console::writeLinef(
          '* Method %s() invokation (without parameters) results in [%s]%s', 
          $m->getName(),
          xp::typeOf($result),
          var_export($result, 1)
        );
      }
    }
  }
  // }}}
?>
