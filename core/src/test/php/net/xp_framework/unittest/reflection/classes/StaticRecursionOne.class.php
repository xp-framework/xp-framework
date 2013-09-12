<?php namespace net\xp_framework\unittest\reflection\classes;



/**
 * Class that loads a class inside its static initializer
 *
 * @see      xp://net.xp_framework.unittest.reflection.ClassLoaderTest#loadClassFileWithRecusionInStaticBlock
 * @purpose  Fixture
 */
class StaticRecursionOne extends \lang\Object {
  public static $two= null;

  static function __static() {
  
    // Load a class here
    self::$two= \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.StaticRecursionTwo');
  }
}
