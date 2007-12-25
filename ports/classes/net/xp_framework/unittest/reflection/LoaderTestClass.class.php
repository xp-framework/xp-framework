<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Test class
   *
   * @see      xp://net.xp_framework.unittest.reflection.ClassLoaderTest
   * @purpose  Test class
   */
  class LoaderTestClass extends Object {
    protected static
      $initializerCalled= FALSE;

    /**
     * Static initializer
     *
     */
    public static function __static() {
      self::$initializerCalled= TRUE;
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
?>
