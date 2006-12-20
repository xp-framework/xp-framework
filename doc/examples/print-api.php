<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');

  // {{{ main
  $p= new ParamString();
  if (!ClassLoader::getDefault()->findClass($p->value(1))) {
    Console::writeLinef('Class "%s" could not be found', $p->value(1));
    exit();
  }

  try {
    $class= XPClass::forName($p->value(1));
  } catch (ClassNotFoundException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine($class->toString(), ' extends ', $class->getParentClass()->toString(), ' {');
  foreach ($class->getFields() as $field) {
    Console::writeLine('  ', $field->toString());
  }
  if ($class->hasConstructor()) {
    Console::writeLine('  ', $class->getConstructor()->toString());
  }
  foreach ($class->getMethods() as $method) {
    Console::writeLine('  ', $method->toString());
  }
  Console::writeLine('}');
  // }}}
?>
