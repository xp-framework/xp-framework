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
  class FaultException extends Exception {
    var
      $fault  = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &scriptlet.rpc.RpcFault fault
     */
    function __construct(&$fault) {
      $this->fault= &$fault;
      parent::__construct($this->fault->faultString);
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
