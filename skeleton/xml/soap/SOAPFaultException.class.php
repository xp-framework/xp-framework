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
    
    /**
     * "Stack Trace" zurückgeben
     *
     * @return  string der StackTrace, vorformatiert
     */
    function getStackTrace() {
      return parent::getStackTrace().sprintf(
        "  [\n    fault.faultcode= '%s'\n    fault.faultstring= '%s'\n    fault.faultactor= '%s'\n    fault.detail= '%s'\n  ]\n",
        $this->fault->faultcode,
        $this->fault->faultstring,
        $this->fault->faultactor,
        trim(chop($this->fault->detail))
      );
    }
  }
?>
