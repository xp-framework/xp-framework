<?php namespace net\xp_framework\unittest\core;

/**
 * Denotes a class is a callback for a Destroyable
 *
 * @see  xp://net.xp_framework.unittest.core.Destroyable
 */
interface DestructionCallback {

  /**
   * Callback for Destroyable class
   *
   * @param   lang.Object object
   */
  public function onDestruction($object);

}
