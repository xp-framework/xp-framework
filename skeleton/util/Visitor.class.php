<?php
/* This class is part of the XP framework
 *
 * $Id:$
 */

  /**
   * Visitor is an interface for classes implementing the visitor pattern.
   *
   * @see       xp://util.Composite
   * @purpose   Interface
   */
  class Visitor extends Interface {

    /**
     * Visits the given Component. Work on the visited objects
     * is up to implementation :)
     *
     * @access  public
     * @param   &util.Component Component
     */
    function visit(&$Component) { }
  }
?>
