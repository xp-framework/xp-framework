<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses(
    'util.profiling.unittest.TestSuite',
    'util.cmd.ParamString',
    'util.Properties'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    printf("Usage: %s configuration\n", $p->value(0));
    exit(-1);
  }
  
  $config= &new Properties($p->value(1));
  $suite= &new TestSuite();
  
  $section= $config->getFirstSection();
  do {
    for ($i= 0, $s= $config->readInteger($section, 'numtests'); $i < $s; $i++) {
      try(); {
        $class= &XPClass::forName($config->readString($section, 'test.'.$i.'.class'));
      } if (catch('ClassNotFoundException', $e)) {
        echo '*** Error: Test group "'.$section.'", test #'.$i.':: ';
        $e->printStackTrace();
        exit(-2);
      }
      
      $name= $config->readString($section, 'test.'.$i.'.name');
      $arguments= array_merge($name, $config->readArray($section, 'test.'.$i.'.args'));
      $suite->addTest(call_user_func_array(
        array(&$class, 'newInstance'), 
        $arguments
      ));
    }
  } while ($section= $config->getNextSection());
  
  $result= &$suite->run();
  echo $result->toString();
  // }}}
?>
