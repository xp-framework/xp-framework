<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Class Interface is the root of all interfaces.
   *
   * @purpose  
   */
  class Interface {
  
    /**
     * Constructor. Ensures interfaces cannot be instanciated.
     * 
     * @access  private
     */
    function Interface() {
      $e= &new Error('Interfaces cannot be instanciated');
      $e->printStackTrace();
      exit(0x7f);
    }
  }
?>
