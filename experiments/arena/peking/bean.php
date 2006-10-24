<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'lang.archive.Archive',
    'io.File'
  );
  
  // {{{ void addClass(io.cca.Archive package, io.Stream name, string name
  //     Adds specified class to package using the classloader
  function addClass(&$package, &$stream, $name) {
    Console::writeLine('---> Adding ', $name);
    $package->add($stream, strtr($name, '.', '/'). '.class.php');
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
  class name + ".xar"
__
    );
    exit(1);
  }
  
  $classname= $p->value(1);
  $cl= &ClassLoader::getDefault();

  // Load bean class
  try(); {
    $class= &$cl->loadClass($classname);
    if (!$class->hasAnnotation('bean')) {
      Console::writeLine('*** ', $classname, ' is not a bean!');
      exit(-2);
    }

    $type= $class->getAnnotation('bean', 'type');
    $name= $class->getAnnotation('bean', 'name');
  } if (catch('ClassNotFoundException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Calculate package name (including trailing ".")
  $pos= strrpos($classname, '.');
  $package= substr($classname, 0, $pos).'.';
  $short= substr($classname, $pos+ 1);
  
  // Generate remote interface
  with ($rstr= &new Stream()); {
    $rstr->open(STREAM_MODE_WRITE);
    $interface= basename($name);
    $rstr->write("<?php\n");
    $rstr->write("  uses('remote.beans.BeanInterface');\n");
    $rstr->write('  class '.$interface." extends BeanInterface {\n");
    foreach ($class->getMethods() as $method) {
      if (!$method->hasAnnotation('remote')) continue;

      $rstr->write("    /**\n     * @access  public\n");
      foreach ($method->getArguments() as $argument) {
        $rstr->write('     * @param  '.$argument->getType().' '.$argument->getName()."\n");
      }
      $rstr->write('     * @return '.$method->getReturnType()."\n");
      $rstr->write("     */\n");
      $rstr->write('    function '.$method->getName(TRUE).'(');
      $args= '';
      foreach ($method->getArguments() as $argument) {
        $args.= '$'.$argument->getName().', ';
      }
      $rstr->write(rtrim($args, ', ').") {}\n");
    }
    $rstr->write("  }\n?>");
    $rstr->close();
  }
  
  // Generate bean implementation
  with ($istr= &new Stream()); {
    $istr->open(STREAM_MODE_WRITE);
    $implementation= $short.'Impl';
    $istr->write("<?php\n");
    $istr->write("  uses('".$package.$short."');\n");
    $istr->write('  class '.$implementation.' extends '.$short." {\n");
    $istr->write('  } implements(__FILE__, \''.$package.$interface."');\n?>");
    $istr->close();
  }

  // Create meta information  
  $meta= &new Stream();
  $meta->open(STREAM_MODE_WRITE);
  $meta->write("[bean]\n");
  $meta->write('class="'.$package.$implementation.'"'."\n");
  $meta->write('remote="'.$package.$interface.'"'."\n");
  $meta->write('lookup="'.$name.'"'."\n");
  $meta->close();
  
  // Package it
  $a= &new Archive(new File($p->value('output', 'o', $classname.'.xar')));
  Console::writeLine('===> Packaging ', $classname, ' into ', $a->toString());
  try(); {
    $a->open(ARCHIVE_CREATE);
    
    // Add meta information
    $a->add($meta, 'META-INF/bean.properties');
    
    // Add: Bean class, remote interface, and home interface
    addClass($a, new File($cl->findClass($class->getName())), $class->getName());
    addClass($a, $rstr, $package.$interface);
    addClass($a, $istr, $package.$implementation);

    $a->create();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  Console::writeLine('===> Done');
  // }}}
?>
