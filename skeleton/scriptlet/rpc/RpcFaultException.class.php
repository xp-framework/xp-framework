<?php
/* This class is part of the XP framework
 *
 * $Id: XmlRpcFaultException.class.php 6701 2006-03-27 17:27:39Z kiesel $ 
 */

  uses('scriptlet.rpc.RpcFault');

  /**
   * Indicates a RPC error occurred.
   *
   * @purpose  Exception
   */
  class RpcFaultException extends Exception {
    var
      $fault  = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &scriptlet.rpc.RpcFault fault
     */
    function __construct(&$fault) {
      parent::__construct($fault->faultString);
      $this->fault= &$fault;
    }

    /**
     * Get Fault
     *
     * @access  public
     * @return  &scriptlet.rpc.RpcFault
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
