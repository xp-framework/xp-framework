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
    public
      $address= NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.telephony.TelephonyAddress address
     */
    public function __construct(TelephonyAddress $address) {
      
      $this->address= $address;
    }
    
    /**
     * Retrieve the terminal's phone number
     *
     * @access  public
     * @return  string number
     */
    public function getAttachedNumber() {
      return $this->address->getNumber();
    }
  }
?>
