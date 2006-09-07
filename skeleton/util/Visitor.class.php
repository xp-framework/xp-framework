<?php
/* This class is part of the XP framework
 *
 * $Id:$
 */

  /**
   * Visitor is an interface for classes implementing the visitor pattern.
   *
   * @see       design pattern: visitor/compositum
   * @purpose   Interface
   */
  class Visitor extends Interface {

    /**
     * Visits the given Object and its children. Works on the visited objects
     * as implemented. 
     *
     * @access  public
     * @param   &lang.Object Object
     */
    function visit(&$Object) { }
  }
?>
