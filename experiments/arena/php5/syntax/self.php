<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  class A {
    public static function getInstance() {
      return new self();
    }
  }
  
  class B extends A {}
  
  // {{{ main
  var_dump(A::getInstance(), B::getInstance());
  // }}}
?>
