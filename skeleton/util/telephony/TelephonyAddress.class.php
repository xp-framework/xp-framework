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
    var
      $number   = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string number
     */
    function __construct($number) {
      
      $this->number= $number;
    }
    
    /**
     * Set Number
     *
     * @access  public
     * @param   string number
     */
    function setNumber($number) {
      $this->number= $number;
    }

    /**
     * Get Number
     *
     * @access  public
     * @return  string
     */
    function getNumber() {
      return $this->number;
    }
  }
?>
