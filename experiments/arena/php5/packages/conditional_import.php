<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  // {{{ package lang
  package lang {
    class Object { }
    class Exception extends Object { }
  }
  // }}}

  // {{{ main
  foreach (get_declared_classes('lang') as $class) {
    if (class_exists(substr($class, strrpos($class, '~')+ 1))) {
      printf("Not importing class %s to avoid name clash\n", $class);
    } else {
      printf("Importing class %s\n", $class);
      import $class;
    }
  }
  // }}}
?>
