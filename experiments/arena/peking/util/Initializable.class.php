<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Classes implementing the initializable interface define
   * they can be initialized and finalized by so named
   * methods
   *
   * @purpose  Interface
   */
  class Initializable extends Interface {

    /**
     * Initialize the class
     *
     * @access  public
     */
    function initialize() { }

    /**
     * Finalize the class
     *
     * @access  public
     */
    function finalize() { }

  
  }
?>
