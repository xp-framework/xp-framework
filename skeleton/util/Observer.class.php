<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Observer interface
   *
   * @see      &util.Observable
   * @purpose  Interface
   */
  class Observer extends Interface {
  
    /**
     * Update method
     *
     * @access  public
     * @param   &util.Observable obs
     * @param   mixed arg default NULL
     */
    function update(&$obs, $arg= NULL) { }
  
  }
?>
