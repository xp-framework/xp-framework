<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  
  // {{{ class CalculatorService
  [@webservice(name= 'Calculator')]
  class CalculatorService {

    protected function log() {
      for ($i= 0, $n= func_num_args(); $i < $n; $i++) {
        var_export(func_get_arg($i));
        echo ' ';
      }
    }

    [@webmethod]
    public function add($a, $b) {
      $this->log('Adding', $a, 'and', $b);
      return $a + $b;
    }

    [@webmethod]
    public function subtract($a, $b) {
      $this->log('Subtracting', $b, 'from', $a);
      return $a - $b;
    }
    
    [@webmethod, @deprecated('Use subtract() instead')]
    public function sub($a, $b) {
      return $this->subtract($a, $b);
    }
  }
  // }}}
  
  // {{{ main
  $c= new ReflectionClass('CalculatorService');
  printf(
    "The webservice %s (handled by the class %s) provides the following web methods:\n",
    $c->getAnnotation('webservice', 'name'),
    $c->getName()
  );
  foreach ($c->getMethods() as $method) {
    if (!$method->hasAnnotation('webmethod')) continue;
    
    printf('- %s(): ', $method->getName());
    if ($method->hasAnnotation('deprecated')) {
      echo '[DEPRECATED: ', var_export($method->getAnnotation('deprecated'), 1), ']: ';
    }
    var_dump($method->getAnnotations());
  }
  // }}}
?>
