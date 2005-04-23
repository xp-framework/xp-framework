<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.profiling.Timer', 'util.cmd.ParamString', 'RootDoc');
  
  function exportClass(&$class, $indent= '') {
    echo '[', $class->name(), "] {\n";

    if ($class->superclass) {
      echo $indent.'  + extends ', exportClass(ClassIterator::parse($class->superclass), $indent.'  ');
    }
    while ($class->interfaces->hasNext()) {
      $iface= &$class->interfaces->next();
      echo $indent.'  + implements ', exportClass($iface, $indent.'  ');
    }
    while ($class->usedClasses->hasNext()) {
      $used= &$class->usedClasses->next();
      echo $indent.'  + uses ', exportClass($used, $indent.'  ');
    }
    echo $indent, "}\n";
  }
  
  $timer= &new Timer();
  $timer->start(); {
    $doc= &new RootDoc(new ParamString());
    
    while ($doc->classes->hasNext()) {
      exportClass($doc->classes->next());
    }
  }
  $timer->stop();
  
  printf("\n%.3f seconds\n", $timer->elapsedTime());
  #var_dump(xp::registry('errors'));
?>
