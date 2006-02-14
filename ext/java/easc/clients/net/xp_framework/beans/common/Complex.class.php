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
    var 
      $real= 0, 
      $imag= 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   int real default 0
     * @param   int imag default 0
     */
    function __construct($real= 0, $imag= 0) {
      $this->real= $real;
      $this->imag= $imag;
    }
    
    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->real.' + '.$this->imag.'i';
    }
  }
?>
