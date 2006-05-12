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
    var
      $callback= NULL;

    /**
     * Set Callback
     *
     * @access  public
     * @param   &net.xp_framework.unittest.core.DestructionCallback callback
     */
    function setCallback(&$callback) {
      $this->callback= &$callback;
    }
  
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      $this->callback->onDestruction($this);
    }
  }
?>
