<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Handler
   *
   * @see      http://www.javaspecialists.co.za/archive/Issue043.html
   * @purpose  Interface
   */
  class SignalHandler extends Interface {
  
    /**
     * Handle signal
     *
     * @access  public
     * @param   int sig
     */
    function handle($sig) { }

  }
?>
