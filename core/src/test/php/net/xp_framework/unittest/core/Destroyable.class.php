<?php namespace net\xp_framework\unittest\core;

/**
 * Destroyable
 *
 * @see      xp://net.xp_framework.unittest.core.DestructorTest
 */
class Destroyable extends \lang\Object {
  public $callback= NULL;

  /**
   * Set Callback
   *
   * @param   net.xp_framework.unittest.core.DestructionCallback callback
   */
  public function setCallback($callback) {
    $this->callback= $callback;
  }

  /**
   * Destructor
   */
  public function __destruct() {
    $this->callback->onDestruction($this);
  }
}
