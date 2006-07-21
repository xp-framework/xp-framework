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
     * @access  public
     * @param   string number
     */
    public function __construct($number) {
      
      $this->number= $number;
    }
    
    /**
     * Set Number
     *
     * @access  public
     * @param   string number
     */
    public function setNumber($number) {
      $this->number= $number;
    }

    /**
     * Get Number
     *
     * @access  public
     * @return  string
     */
    public function getNumber() {
      return $this->number;
    }
  }
?>
