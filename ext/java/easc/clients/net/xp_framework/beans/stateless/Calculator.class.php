<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$ 
 */

  uses('net.xp_framework.beans.common.Complex');

  /**
   * Calculator interface
   *
   * @purpose  Demo class  
   */
  class Calculator extends Interface {
  
    /**
     * Adds the two given arguments
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @return  mixed
     */
    function add($a, $b) { }

    /**
     * Subtracts the two given arguments
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @return  mixed
     */
    function subtract($a, $b) { }

    /**
     * Multiplies the two given arguments
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @return  mixed
     */
    function multiply($a, $b) { }
  
  }
?>
