<?php
/* Demonstrate the usage of the different class loaders
 * 
 * Usage:
 * <pre>
 * - php -q classloader.php io.File default
 *   Loads the class io.File with the default Classloader
 *
 * - php -q classloader.php Test net 'http://sitten-polizei.de/php/classes/%s.class.php'
 *   Loads the class Test via HTTP
 *
 * - php -q classloader.php Url external /path/to/eclipse-3_0 %s.php
 *   Loads the class Url from the Eclipse framework (see
 *   http://www.students.cs.uu.nl/people/voostind/eclipse/)
 *
 * - php -q classloader.php Console_Getopt pear /path/to/pear
 * </pre>
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses(
    'util.cmd.ParamString', 
    'lang.ExternalClassLoader', 
    'lang.NetClassLoader',
    'lang.PearClassLoader'
  );
  
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
      
    case 'pear':
      $loader= &new PearClassLoader($p->value(3));
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
