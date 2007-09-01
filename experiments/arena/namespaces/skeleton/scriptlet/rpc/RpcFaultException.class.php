<?php
/* This class is part of the XP framework
 *
 * $Id: RpcFaultException.class.php 8439 2006-11-11 16:45:16Z kiesel $ 
 */

  namespace scriptlet::rpc;

  uses('scriptlet.rpc.RpcFault');

  /**
   * Indicates a RPC error occurred.
   *
   * @purpose  Exception
   */
  class RpcFaultException extends lang::XPException {
    public
      $fault  = NULL;
    
    /**
     * Constructor
     *
     * @param   scriptlet.rpc.RpcFault fault
     */
    public function __construct($fault) {
      parent::__construct($fault->faultString);
      $this->fault= $fault;
    }

    /**
     * Get Fault
     *
     * @return  scriptlet.rpc.RpcFault
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
