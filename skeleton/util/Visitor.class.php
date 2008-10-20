<?php
/* This class is part of the XP framework
 *
 * $Id:$
 */

  /**
   * Visitor is an interface for classes implementing the visitor pattern.
   *
   * @see       xp://util.Component
   * @purpose   Interface
   */
  interface Visitor {

    /**
     * Visits the given Component. Work on the visited objects
     * is up to implementation :)
     *
     * @param   util.Component Component
     */
    public function visit($Component);
  }
?>
