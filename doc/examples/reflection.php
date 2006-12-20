<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');

  // {{{ main
  $p= new ParamString();
  $cl= ClassLoader::getDefault();
  if (!$cl->findClass($p->value(1))) {
    Console::writeLinef('Class "%s" could not be found', $p->value(1));
    exit();
  }

  try {
    $class= XPClass::forName($p->value(1));
    $parent= $class->getParentClass();
  } catch (ClassNotFoundException $e) {
    $e->printStackTrace();
    exit(-1);
  } catch (XPException $e) {
    $e->printStackTrace();
    exit(-2);
  }
  
  // Retrieve class methods
  $methods= '';
  for ($i= 0, $m= $class->getMethods(), $s= sizeof($m); $i < $s; $i++) {
    $decl= $m[$i]->getDeclaringClass();
    $methods.= '  - '.$m[$i]->toString().' declared in '.$decl->getName();
    if ($m[$i]->hasAnnotations()) $methods.= ' [#'.var_export($m[$i]->getAnnotations(), 1).']';
    $methods.="\n";
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

    // Retrieve constructor
    if ($constructor= $class->getConstructor()) {
      $decl= $constructor->getDeclaringClass();
    }
    
    // Retrieve implemented interfaces
    $implements= '';
    for ($c= 0, $i= $class->getInterfaces(), $s= sizeof($i); $c < $s; $c++) {
      $implements.= ', '.$i[$c]->getName();
    }
    
    // Retrieve fields
    $fields= '';
    for ($c= 0, $f= $class->getFields(), $s= sizeof($f); $c < $s; $c++) {
      $fields.= '$'.$f[$c]->getName()."\n    ";
    }

    // Create a new instance
    $instance= $class->newInstance();
  
    Console::writef(
      "Class '%s' (extends %s) %s\n".
      "* Constructor: %s\n".
      "* Methods:\n%s\n".
      "* Fields:\n  - %s\n\n".
      "* Has method 'toString': %s\n\n".
      "* Instance toString() output: %s\n\n",
      $class->getName(),
      $parent ? $parent->getName() : '(n/a)',
      $implements ? 'implements '.substr($implements, 2) : '',
      ($constructor
        ? $constructor->toString().' declared in '.$decl->getName()
        : '(none)'
      ),
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
