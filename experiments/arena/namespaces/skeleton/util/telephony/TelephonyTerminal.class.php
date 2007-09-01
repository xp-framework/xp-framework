<?php
/* This class is part of the XP framework
 *
 * $Id: TelephonyTerminal.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace util::telephony;

  /**
   * Represents a terminal
   *
   */
  class TelephonyTerminal extends lang::Object {
    public 
      $address  = NULL,
      $observed = FALSE;
      
    /**
     * Constructor
     *
     * @param   util.telephony.TelephonyAddress address
     */
    public function __construct($address) {
      
      $this->address= $address;
    }
    
    /**
     * Retrieve the terminal's phone number
     *
     * @return  string number
     */
    public function getAttachedNumber() {
      return $this->address->getNumber();
    }

    /**
     * Set Observed
     *
     * @param   bool observed
     */
    public function setObserved($observed) {
      $this->observed= $observed;
    }

    /**
     * Get Observed state
     *
     * @return  bool
     */
    public function isObserved() {
      return $this->observed;
    }
  }
?>
