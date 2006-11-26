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

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public static function __static() {
      LoaderTestClass::initializerCalled(TRUE);
    }
    
    /**
     * Static variables simulation
     *
     * @model   static
     * @access  public
     * @param   bool value default NULL
     * @return  bool
     */
    public static function initializerCalled($value= NULL) {
      static $called;
      if (NULL !== $value) $called= $value;
      return $called;
    }
  }
?>
