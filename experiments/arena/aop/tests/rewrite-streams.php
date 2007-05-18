<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.invoke.aop.Weaver',
    'util.profiling.Timer'
  );
  
  class Aop extends Object {
    public static $pointcuts= array();
  }
  
  #[@aspect]
  class PowerAspect extends Object {
  
    #[@pointcut]
    public function settingPower() {
      return 'Binford::setPoweredBy';
    }
  
    #[@before('settingPower')]
    public function checkPower($p) {
      if ($p != 6100 && $p != 611) { 
        throw new IllegalArgumentException('Power must either be 611 or 6100'); 
      }
    }

    #[@after('settingPower')]
    public function logPower() {
      Console::writeLine('Power successfully set!');
    }
  }
  
  // Install stream wrapper
  $p= new ParamString();
  if (!$p->exists('disable')) {
    ClassLoader::$transform= 'weave://';
  }
    
  // Register pointcuts
  $pa= new PowerAspect();
  Aop::$pointcuts['Binford']= TRUE;
  Aop::$pointcuts['Binford::setPoweredBy']= array(
    'before' => array($pa, 'checkPower'),
    'after'  => array($pa, 'logPower'),
  );
  
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
