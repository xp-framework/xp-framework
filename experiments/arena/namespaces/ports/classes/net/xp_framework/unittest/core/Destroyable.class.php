<?php
/* This class is part of the XP framework
 *
 * $Id: Destroyable.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::core;

  /**
   * Destroyable
   *
   * @see      xp://net.xp_framework.unittest.core.DestructorTest
   * @purpose  Test class
   */
  class Destroyable extends lang::Object {
    public
      $callback= NULL;

    /**
     * Set Callback
     *
     * @param   &net.xp_framework.unittest.core.DestructionCallback callback
     */
    public function setCallback($callback) {
      $this->callback= $callback;
    }
  
    /**
     * Destructor
     *
     */
    public function __destruct() {
      $this->callback->onDestruction($this);
    }
  }
?>
