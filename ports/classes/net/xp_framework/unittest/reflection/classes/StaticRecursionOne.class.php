<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Class that loads a class inside its static initializer
   *
   * @see      xp://net.xp_framework.unittest.reflection.ClassLoaderTest#loadClassFileWithRecusionInStaticBlock
   * @purpose  Fixture
   */
  class StaticRecursionOne extends Object {
    static function __static() {
    
      // Load a class here
      XPClass::forName('net.xp_framework.unittest.reflection.classes.StaticRecursionTwo');
    }
  }
?>
