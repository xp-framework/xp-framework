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
  class XmlRpcFaultException extends Exception {
    var
      $fault  = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &webservices.xmlrpc.XmlRpcFault fault
     */
    function __construct(&$fault) {
      $this->fault= &$fault;
      parent::__construct($this->fault->faultString);
    }

    /**
     * Get Fault
     *
     * @access  public
     * @return  &webservices.xmlrpc.XmlRpcFault
     */
    function &getFault() {
      return $this->fault;
    }
    
    /**
     * Return a string representation of this exception
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return parent::toString().sprintf(
        "  [\n    fault.faultcode= '%s'\n    fault.faultstring= '%s'\n  ]\n",
        $this->fault->faultCode,
        $this->fault->faultString
      );
    }
  }
?>
