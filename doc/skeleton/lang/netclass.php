<?php
/* Net class loader demo
 * Examples:
 * - php netclass.php de.sitten-polizei.Test
 * - php netclass.php de.sitten-polizei.Foo
 *
 * $Id$
 */
  require('lang.base.php');
  uses(
    'lang.NetClassLoader',
    'util.cmd.ParamString'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (2 != $p->count) {
    printf("Usage: %s <fully qualified class name>\n", $p->value(0));
    exit();
  }
  
  $e= &new NetClassLoader('http://sitten-polizei.de/php/classes/');
  try(); {
    $name= $e->loadClass($p->value(1));
  } if (catch('ClassNotFoundException', $e)) {
  
    // Class or dependency not found
    $e->printStackTrace();
    exit();
  }
  
  // Create an instance
  $obj= &new $name();
  var_dump($obj, $obj->getClassName(), $obj->toString());
  
  // }}}
?>

