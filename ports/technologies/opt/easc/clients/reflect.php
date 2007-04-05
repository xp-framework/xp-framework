<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('remote.Remote', 'util.cmd.ParamString');
  
  // {{{ main
  $p= new ParamString();
  if (!$p->exists(1)) {
    Console::writeLine(<<<__
ESDL demo application

Usage
-----
$ php reflect.php <hostname> [-p <port>] [-b <jndiname> [-c]]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * port is the port the ESDL-MBean is listening on. It defaults to 6449.
  
  * jndiname is the name of the bean. If this parameter is omitted,
    all deployed beans are listed.
  
  * The -c flag lists all classes used by the bean's interfaces
__
    );
    exit(1);
  }
  
  try {
    $remote= Remote::forName('xp://'.$p->value(1).':'.$p->value('port', 'p', 6449).'/');
  } catch (Throwable $e) {
    $e->printStackTrace();
    exit(-1);
  }

  if ($p->exists('bean')) {
    $bean= $remote->lookup('Services:'.$p->value('bean'));
    Console::writeLine(xp::stringOf($bean));
    
    if ($p->exists('classlist')) {
      $cl= &ClassLoader::getDefault();
      $jndi= $bean->getJndiName();
      foreach ($bean->classSet() as $classref) {
        if (!$cl->findClass($classref->referencedName())) {
          try {
            $class= $remote->lookup('Class:'.$jndi.':'.$classref->referencedName());
          } catch (Throwable $e) {
            $e->printStackTrace();
            continue;
          }
        } else {
          $class= $cl->loadClass($classref->referencedName());
        }
        Console::writeLine(xp::stringOf($class));
        xp::gc();
      }
    }
  } else {
    $services= $remote->lookup('Services');
    Console::writeLinef('# Beans found= %d', $services->size());
    foreach ($services->beans() as $description) {
      Console::writeLine(xp::stringOf($description));
    }
  }
  // }}}
?>
