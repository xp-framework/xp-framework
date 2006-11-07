<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');

  // {{{ main
  $p= &new ParamString();
  $cl= &ClassLoader::getDefault();
  if (!$cl->findClass($p->value(1))) {
    Console::writeLinef('Class "%s" could not be found', $p->value(1));
    exit();
  }

  try(); {
    $class= &XPClass::forName($p->value(1));
  } if (catch ('ClassNotFoundException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine($class->toString(), ' extends ', xp::stringOf($class->getParentClass()), ' {');
  foreach ($class->getFields() as $field) {
    Console::writeLine('  ', xp::stringOf($field));
  }
  if ($class->hasConstructor()) {
    Console::writeLine('  ', xp::stringOf($class->getConstructor()));
  }
  foreach($class->getMethods() as $field) {
    Console::writeLine('  ', xp::stringOf($field));
  }
  Console::writeLine('}');
  // }}}
?>
