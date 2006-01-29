<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * This - static inheritance
   *
   * @see      xp://ThisTestCase
   * @purpose  Static inheritance
   */
  class This extends Object {
  
    /**
     * Invokes a method statically
     *
     * @model   static
     * @access  public
     * @param   string name
     * @return  array args default array()
     */
    function invoke($name, $args= array()) {
      $trace= debug_backtrace();
      return call_user_func_array(array($trace[1]['class'], $name), $args);
    }
  }
?>
