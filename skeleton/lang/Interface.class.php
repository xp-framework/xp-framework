<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Class Interface is the root of all interfaces.
   *
   * @purpose  Interface
   */
  class Interface {
  
    /**
     * Constructor. Ensures interfaces cannot be instantiated.
     * 
     * @access  private
     */
    function Interface() {
      xp::error('Interfaces cannot be instantiated ('.get_class($this).')');
    }
  }
?>
