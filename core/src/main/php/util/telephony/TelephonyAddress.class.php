<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Represents an address
   *
   * @purpose  an abstract wrapper for addresses
   */
  class TelephonyAddress extends Object {
    public
      $number   = '';

    /**
     * Constructor
     *
     * @param   string number
     */
    public function __construct($number) {
      
      $this->number= $number;
    }
    
    /**
     * Set Number
     *
     * @param   string number
     */
    public function setNumber($number) {
      $this->number= $number;
    }

    /**
     * Get Number
     *
     * @return  string
     */
    public function getNumber() {
      return $this->number;
    }
  }
?>
