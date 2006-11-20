<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Denotes a class is a callback for a Destroyable
   *
   * @see      xp://net.xp_framework.unittest.core.Destroyable
   * @purpose  Test class
   */
  class DestructionCallback extends Interface {

    /**
     * Callback for Destroyable class
     *
     * @access  public
     * @param   &lang.Object object
     */
    function onDestruction(&$object) { }
  
  }
?>
