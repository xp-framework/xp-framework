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
      $address= NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.telephony.TelephonyAddress address
     */
    function __construct(&$address) {
      parent::__construct();
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
  }
?>
