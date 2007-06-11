<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.xmlrpc.XmlRpcFault');

  /**
   * Indicates a XML-RPC error occurred.
   *
   * @purpose  Exception
   */
  class XmlRpcFaultException extends XPException {
    public
      $fault  = NULL;
    
    /**
     * Constructor
     *
     * @param   webservices.xmlrpc.XmlRpcFault fault
     */
    public function __construct($fault) {
      parent::__construct($fault->faultString);
      $this->fault= $fault;
    }

    /**
     * Get Fault
     *
     * @return  webservices.xmlrpc.XmlRpcFault
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
        "  fault.faultstring = '%s'\n".
        "}\n",
        $this->getClassName(),
        $this->message,
        $this->fault->faultCode,
        $this->fault->faultString
      );
    }
  }
?>
