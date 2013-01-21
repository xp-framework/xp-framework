<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.reflection.PrivateAccessibilityFixture');

  /**
   * Fixture class for accessibility tests
   *
   * @see      xp://net.xp_framework.unittest.reflection.PrivateAccessibilityTest
   */
  class PrivateAccessibilityFixtureCtorChild extends PrivateAccessibilityFixture {

    /**
     * Entry point: Invoke constructor
     *
     * @param   lang.XPClass
     * @return  net.xp_framework.unittest.reflection.PrivateAccessibilityFixture
     */
    public static function construct(XPClass $class) {
      return $class->getConstructor()->newInstance(array());
    }
  }
?>
