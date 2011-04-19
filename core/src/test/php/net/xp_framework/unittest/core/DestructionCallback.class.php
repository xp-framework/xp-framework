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
  interface DestructionCallback {

    /**
     * Callback for Destroyable class
     *
     * @param   &lang.Object object
     */
    public function onDestruction($object);
  
  }
?>
