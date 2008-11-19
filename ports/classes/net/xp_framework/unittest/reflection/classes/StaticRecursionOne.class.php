<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class StaticRecursionOne extends Object {
    static function __static() {
    
      // Load a class here
      XPClass::forName('net.xp_framework.unittest.reflection.StaticRecursionTwo');
    }
  }
?>
