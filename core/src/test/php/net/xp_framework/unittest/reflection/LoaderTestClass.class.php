<?php namespace net\xp_framework\unittest\reflection;

/**
 * Test class
 *
 * @see      xp://net.xp_framework.unittest.reflection.ClassLoaderTest
 * @purpose  Test class
 */
class LoaderTestClass extends \lang\Object {
  protected static
    $initializerCalled= false;

  /**
   * Static initializer
   *
   */
  public static function __static() {
    self::$initializerCalled= true;
  }
  
  /**
   * Returns whether the static initializer was called
   *
   * @return  bool
   */
  public static function initializerCalled() {
    return self::$initializerCalled;
  }
}
