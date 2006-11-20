5<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.xmlrpc.XmlRpcFault');

  /**
   * Indicates a XML-RPC error occurred.
   *
   * @purpose  Exception
   */
  class XmlRpcFaultException extends Exception {
    var
      $fault  = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &xml.xmlrpc.XmlRpcFault fault
     */
    function __construct(&$fault) {
      parent::__construct($fault->faultString);
      $this->fault= &$fault;
    }

    /**
     * Get Fault
     *
     * @access  public
     * @return  &xml.xmlrpc.XmlRpcFault
     */
    function &getFault() {
      return $this->fault;
    }
    
    /**
     * Return compound message of this exception.
     *
     * @access  public
     * @return  string
     */
    function compoundMessage() {
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
