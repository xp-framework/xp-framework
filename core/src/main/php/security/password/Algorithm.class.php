<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  $package= 'security.password';

  /**
   * Defines an algorithm that calculates the strength of a password
   *
   * @purpose  Interface
   */
  interface security·password·Algorithm {
    
    /**
     * Calculate the strength of a password
     *
     * @param   string password
     * @return  int
     */
    public function strengthOf($password);
  }
?>
