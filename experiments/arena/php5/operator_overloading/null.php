<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

  // {{{ class NullPointerException
  class NullPointerException extends Exception { }

  // {{{ final class null
  final class null {
    public static $instance= NULL;
    
    public static operator ! (Null $n) {
      return TRUE;
    }
    
    public function __call($name, $args) {
      throw new NullPointerException($name);
    }
  }
  null::$instance= new Null();
  // }}}
  
  // {{{ class UserClass
  class UserClass {
    function getConstructor() {
      return null::$instance;
    }
  }
  // }}}
  
  // {{{ main
  Reflection::export(new ReflectionClass('null'));
  
  $u= new UserClass();
  if (!($constructor= $u->getConstructor())) {
    printf("No constructor for class %s\n", get_class($u));
    // exit missing intentionally
  }

  var_dump($constructor->invoke());
  // }}}
?>
