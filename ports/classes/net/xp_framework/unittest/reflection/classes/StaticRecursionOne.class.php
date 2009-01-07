<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.reflection.classes.StaticRecursionTwo');

  /**
   * Class that loads a class inside its static initializer
   *
   * @see      xp://net.xp_framework.unittest.reflection.ClassLoaderTest#loadClassFileWithRecusionInStaticBlock
   * @purpose  Fixture
   */
  class StaticRecursionOne extends Object {
    public static $two= NULL;

    static function __static() {
    
      // Load a class here
      self::$two= XPClass::forName('net.xp_framework.unittest.reflection.classes.StaticRecursionTwo');
    }
  }
?>
