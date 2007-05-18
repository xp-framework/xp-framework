<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.invoke.aop.Aspects',
    'util.invoke.aop.Weaver',
    'util.profiling.Timer'
  );
  
  class Aop extends Object {
    public static $pointcuts= array();
    
    public static function register(Generic $aspect) {
      foreach ($aspect->getClass()->getMethods() as $m) {
        if ($m->hasAnnotation('pointcut')) {
          sscanf($m->getAnnotation('pointcut'), '%[^:]::%s', $classname, $method);
          self::$pointcuts[$classname][$method]= array();
          $p[$m->getName()]= &self::$pointcuts[$classname][$method];
        } else if ($m->hasAnnotation('before')) {
          $p[$m->getAnnotation('before')]['before']= array($aspect, $m->getName());
        } else if ($m->hasAnnotation('after')) {
          $p[$m->getAnnotation('after')]['after']= array($aspect, $m->getName());
        } else if ($m->hasAnnotation('throwing')) {
          $p[$m->getAnnotation('throwing')]['throwing']= array($aspect, $m->getName());
        }
      }
    }
  }
  
  // Install stream wrapper
  $p= new ParamString();
  if (!$p->exists('disable')) {
    XPClass::forName('aspects.PowerAspect');
    ClassLoader::$transform= 'weave://%2$s|%1$s';
  }
  
  // Register fqcns.xar
  ClassLoader::registerLoader(new ArchiveClassLoader(new ArchiveReader(
    dirname(__FILE__).DIRECTORY_SEPARATOR.'fqcns.xar'
  )));

  // Load binford class
  $t= new Timer();
  $t->start();
  XPClass::forName('util.Binford');
  XPClass::forName('info.binford6100.Date');
  $t->stop();
  
  // Create an instance
  $bf= new Binford($p->value(1, NULL, 6100));
  $bd= new info·binford6100·Date();  
  
  // Profiling
  Console::writeLinef('%s && %s - took %.3f seconds', $bf->toString(), $bd->toString(), $t->elapsedTime());
?>
