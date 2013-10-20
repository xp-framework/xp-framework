<?php namespace net\xp_framework\unittest\reflection;

/**
 * Interface with overloaded methods
 *
 * @see      xp://lang.reflect.Proxy
 * @purpose  Test interface
 */
interface OverloadedInterface {
  
  /**
   * Overloaded method.
   *
   */
  #[@overloaded(signatures= array(
  #  array('string'),
  #  array('string', 'string')
  #))]
  public function overloaded();
}
