<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Represents a call
   *
   */
  class TelephonyCall extends Object {
    var 
      $origin       = NULL,
      $destination  = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.telephony.TelephonyAddress origin
     * @param   &util.telephony.TelephonyAddress destination
     */
    function __construct(&$origin, &$destination) {
      $this->origin= &$origin;
      $this->destination= &$destination;
      parent::__construct();
    }
    
    /**
     * Retrieve the origin's phone number
     *
     * @access  public
     * @return  string number
     */
    function getOriginNumber() {
      return $this->origin->toString();
    }

    /**
     * Retrieve the destination's phone number
     *
     * @access  public
     * @return  string number
     */
    function getDestinationNumber() {
      return $this->destination->toString();
    }
  }
?>
