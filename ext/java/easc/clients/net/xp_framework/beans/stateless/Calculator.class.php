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
  interface Calculator {
  
    /**
     * Adds the two given arguments
     *
     * @param   mixed a
     * @param   mixed b
     * @return  mixed
     */
    public function add($a, $b);

    /**
     * Subtracts the two given arguments
     *
     * @param   mixed a
     * @param   mixed b
     * @return  mixed
     */
    public function subtract($a, $b);

    /**
     * Multiplies the two given arguments
     *
     * @param   mixed a
     * @param   mixed b
     * @return  mixed
     */
    public function multiply($a, $b);
  
  }
?>
