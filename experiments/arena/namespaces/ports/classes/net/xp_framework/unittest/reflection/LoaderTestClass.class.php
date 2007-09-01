<?php
/* This class is part of the XP framework
 *
 * $Id: LoaderTestClass.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::reflection;

  /**
   * Test class
   *
   * @see      xp://net.xp_framework.unittest.reflection.ClassLoaderTest
   * @purpose  Test class
   */
  class LoaderTestClass extends lang::Object {

    /**
     * Static initializer
     *
     */
    public static function __static() {
      LoaderTestClass::initializerCalled(TRUE);
    }
    
    /**
     * Static variables simulation
     *
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
