<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  class A {
    protected static $instance = NULL;
    
    public static function getInstance() {
      if (!self::$instance) {
        self::$instance= new self();
      }
      return self::$instance;
    }
  }
  
  class B extends A {}
  
  // {{{ main
  var_dump(A::getInstance(), B::getInstance());
  // }}}
?>
