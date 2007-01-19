<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$ 
 */
 
  /**
   * Complex
   *
   * @purpose  Demo class  
   */
  class Complex extends Object {
    public 
      $real= 0, 
      $imag= 0;
    
    /**
     * Constructor
     *
     * @param   int real default 0
     * @param   int imag default 0
     */
    public function __construct($real= 0, $imag= 0) {
      $this->real= $real;
      $this->imag= $imag;
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->real.' + '.$this->imag.'i';
    }
  }
?>
