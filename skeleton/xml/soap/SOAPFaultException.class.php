<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */

  class SOAPFaultException extends Exception {
    var 
      $fault= NULL; 
      
    /**
     * Constructor
     *
     * @param   xml.soap.SOAPFault fault Ein Objekt des Typen SOAPFault
     */
    function __construct(&$fault) {
      $this->fault= $fault;
      parent::__construct($this->fault->faultstring);
    }
  }
?>
