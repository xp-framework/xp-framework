<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */


  // {{{ void imports(string spec)
  //     Static imports
  function imports($spec) {
    sscanf($spec, '%[^:]::%s', $class, $what);
    $name= xp::reflect($class);
    
    try {
      $reflect= new ReflectionClass($name);
      if ('*' === $what) {
        $candidates= $reflect->getMethods(ReflectionMethod::IS_STATIC);
      } else {
        $m= $reflect->getMethod($what);
        if (!$m->isStatic()) xp::error('[imports] '.$spec.' is not static');
        $candidates= array($m);
      }
    } catch (ReflectionException $e) {
      xp::error('[imports] '.$e->getMessage());
    }
      
    foreach ($candidates as $method) {
      if (
        '__static' == $method->name || 
        function_exists($method->name) ||
        $method->isAbstract()
      ) continue;

      // Declare delegating function
      eval(sprintf('function %1$s() { $a= func_get_args(); 
        return call_user_func_array(array(\'%2$s\', \'%1$s\'), $a); 
      }', $method->name, $name));
    }
  }
  // }}}
?>
