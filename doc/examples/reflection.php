<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');

  $p= &new ParamString();

  try(); {
    $class= &XPClass::forName($p->value(1));
    $instance= &$class->newInstance();
  } if (catch ('ClassNotFoundException', $e)) {
    $e->printStackTrace();
    exit(-1);
  } if (catch ('Exception', $e)) {
    $e->printStackTrace();
    exit(-2);
  }
  
  $methods= '';
  for ($i= 0, $m= $class->getMethods(), $s= sizeof($m); $i < $s; $i++) {
    $class= &$m[$i]->getDeclaringClass();
    $methods.= '  - '.$m[$i]->getName().'() [from '.$class->getName()."]\n";
  }

  Console::writef(
    "Class '%s'\n".
    "* Methods:\n%s\n".
    "* Fields:\n  - \$%s\n\n".
    "* Has method 'toString': %s\n\n".
    "* Instance toString() output: %s\n\n",
    $class->getName(),
    $methods,
    implode("\n  - \$", array_keys($class->getFields())),
    var_export($class->hasMethod('toString'), 1),
    $instance->toString()
  );
  
  if ($p->exists('invoke')) {
    with ($m= &$class->getMethod($p->value('invoke'))); {
      $result= &$m->invoke($class);
      Console::writeLinef(
        '* Method %s() invokation (without parameters) results in [%s]%s', 
        $m->getName(),
        xp::typeOf($result),
        var_export($result, 1)
      );
    }
  }
?>
