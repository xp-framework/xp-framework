<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Represents a terminal
   *
   */
  class TelephonyTerminal extends Object {
    var 
      $address  = NULL,
      $observed = FALSE;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.telephony.TelephonyAddress address
     */
    function __construct(&$address) {
      
      $this->address= &$address;
    }
    
    /**
     * Retrieve the terminal's phone number
     *
     * @access  public
     * @return  string number
     */
    function getAttachedNumber() {
      return $this->address->getNumber();
    }

    /**
     * Set Observed
     *
     * @access  public
     * @param   bool observed
     */
    function setObserved($observed) {
      $this->observed= $observed;
    }

    /**
     * Get Observed state
     *
     * @access  public
     * @return  bool
     */
    function isObserved() {
      return $this->observed;
    }
  }
?>
