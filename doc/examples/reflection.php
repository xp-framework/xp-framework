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

  Console::writef(
    "Class '%s'\n".
    "* Methods:\n  - %s()\n\n".
    "* Fields:\n  - \$%s\n\n".
    "* Has method 'toString': %s\n\n".
    "* Instance toString() output: %s\n",
    $class->getName(),
    implode("()\n  - ", $class->getMethods()),
    implode("\n  - \$", array_keys($class->getFields())),
    var_export($class->hasMethod('toString'), 1),
    $instance->toString()
  );
?>
