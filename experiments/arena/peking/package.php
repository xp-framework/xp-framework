<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.cca.Archive',
    'io.File'
  );
  
  // {{{ void addClass(io.cca.Archive package, lang.ClassLoader classloader, lang.XPClass class
  //     Adds specified class to package using the classloader
  function addClass(&$package, &$classloader, &$class) {
    Console::writeLine('---> Adding ', $class->toString());
    $package->add(
      new File($classloader->findClass($class->getName())), 
      $class->getName()
    );
  }
  // }}}
  
  
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLine(<<<__
Packages a bean class for deployment

Usage:
$ php package.php <class-name> [ -o <package-file>]

* class-name is the fully-qualified class name of the bean
  to package

* package-file is the package's filename and defaults to the
  class name + ".ear"
__
    );
    exit(1);
  }
  
  $classname= $p->value(1);
  $cl= &ClassLoader::getDefault();

  // Load bean class, home interface and remote interface
  try(); {
    $class= &$cl->loadClass($classname);
    if (!$class->isSubclassOf('remote.beans.Bean')) {
      Console::writeLine('*** ', $classname, ' is not a bean!');
      exit(-2);
    }
    
    $home= &$cl->loadClass($class->getAnnotation('homeInterface'));
    
    $remote= NULL;
    foreach ($class->getInterfaces() as $interface) {
      if (!$interface->isSubclassOf('remote.beans.RemoteInterface')) continue;

      $remote= &$interface;
      break;
    }
  } if (catch('ClassNotFoundException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Create meta information  
  $meta= &new Stream();
  $meta->open(STREAM_MODE_WRITE);
  $meta->write("[bean]\n");
  $meta->write('class="'.$classname.'"');
  $meta->close();
  
  // Package it
  $a= &new Archive(new File($p->value('output', 'o', $classname.'.ear')));
  Console::writeLine('===> Packaging ', $classname, ' into ', $a->toString());
  try(); {
    $a->open(ARCHIVE_CREATE);
    
    // Add meta information
    $a->add($meta, 'META-INF/bean.properties');
    
    // Add: Bean class, remote interface, and home interface
    addClass($a, $cl, $class);
    addClass($a, $cl, $remote);
    addClass($a, $cl, $home);

    $a->create();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  Console::writeLine('===> Done');
  // }}}
?>
