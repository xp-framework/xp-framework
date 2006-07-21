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
  interface Observer {
  
    /**
     * Update method
     *
     * @access  public
     * @param   &util.Observable obs
     * @param   mixed arg default NULL
     */
    public function update(&$obs, $arg= NULL);
  
  }
?>
