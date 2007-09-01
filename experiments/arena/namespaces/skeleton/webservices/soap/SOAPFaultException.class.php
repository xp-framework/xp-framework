<?php
/* This class is part of the XP framework
 *
 * $Id: SOAPFaultException.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace webservices::soap;

  /**
   * Indicates a SOAP fault occured
   *
   * @purpose  Exception
   */
  class SOAPFaultException extends lang::XPException {
    public 
      $fault= NULL; 

    /**
     * Constructor
     *
     * @param   webservices.soap.CommonSoapFault fault
     */
    public function __construct($fault) {
      parent::__construct($fault->faultstring);
      $this->fault= $fault;
    }

    /**
     * Get Fault
     *
     * @return  webservices.soap.SOAPFault
     */
    public function getFault() {
      return $this->fault;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        "Exception %s (%s) {\n".
        "  fault.faultcode   = %s\n".
        "  fault.faultactor  = '%s'\n".
        "  fault.detail      = %s\n".
        "}\n",
        $this->getClassName(),
        $this->message,
        $this->fault->faultcode,
        $this->fault->faultactor,
        ::xp::stringOf($this->fault->detail)
      );
    }
  }
?>
