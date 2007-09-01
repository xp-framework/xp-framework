<?php
/* This class is part of the XP framework
 *
 * $Id: DestructionCallback.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::core;

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
