<?php
/* Demonstrate the usage of the different class loaders
 * 
 * Usage:
 * <pre>
 * - php -q classloader.php io.File
 * - php -q classloader.php io.File default
 * - php -q classloader.php io.File default
 * </pre>
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('util.cmd.ParamString', 'lang.ExternalClassLoader', 'lang.NetClassLoader');
  
  // {{{ main
  $p= &new ParamString();
  $l= $p->exists(2) ? $p->value(2) : 'default';
  switch($l) {
    case 'external':
      $loader= &new ExternalClassLoader($p->value(3), $p->value(4));
      break;
      
    case 'net':
      $loader= &new NetClassLoader($p->value(3));
      break;
      
    case 'default':
      $loader= &new ClassLoader();
      break;
      
    default:
      printf("Unknown classloader '%s'\n", $l);
      exit();
  }
  
  try(); {
    $name= $loader->loadClass($p->value(1));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  $obj= &new $name();
  var_dump(XPClass::getClasses());
  var_dump($obj);
  // }}}
  
?>
