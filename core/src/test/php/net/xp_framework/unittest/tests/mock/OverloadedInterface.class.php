<?php
/* This class is part of the XP framework
 *
 * $Id: OverloadedInterface.class.php 11704 2008-01-12 21:54:46Z friebe $ 
 */

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
?>
