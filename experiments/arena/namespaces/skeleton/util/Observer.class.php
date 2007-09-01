<?php
/* This class is part of the XP framework
 *
 * $Id: Observer.class.php 9198 2007-01-08 19:40:23Z friebe $ 
 */

  namespace util;

  /**
   * Observer interface
   *
   * @see      xp://util.Observable
   * @purpose  Interface
   */
  interface Observer {
  
    /**
     * Update method
     *
     * @param   util.Observable obs
     * @param   mixed arg default NULL
     */
    public function update($obs, $arg= NULL);
  
  }
?>
