<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Destroyable
   *
   * @see      xp://net.xp_framework.unittest.core.DestructorTest
   * @purpose  Test class
   */
  class Destroyable extends Object {
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
