<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Thrown to indicate that the clone method in class Object has been called
   * to clone an object, but that the object's class does not implement the
   * Cloneable interface.
   * 
   * Applications that override the clone method can also throw this exception
   * to indicate that an object could not or should not be cloned. 
   *
   * @test  xp://net.xp_framework.unittest.core.CloningTest
   */
  class CloneNotSupportedException extends XPException {
  }
?>
