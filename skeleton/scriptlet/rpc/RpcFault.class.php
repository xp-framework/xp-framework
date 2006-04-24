<?php
/* This class is part of the XP framework
 *
 * $Id: XmlRpcFault.class.php 6701 2006-03-27 17:27:39Z kiesel $ 
 */

  /**
   * Represent an RPC Fault.
   *
   * @purpose  Wrap fault
   */
  class RpcFault extends Object {
    var
      $faultCode=     0,
      $faultString=   '';

    /**
     * Constructor.
     *
     * @access  public
     * @param   int code
     * @param   string string
     */
    function __construct($code, $string) {
      $this->faultCode= $code;
      $this->faultString= $string;
    }

    /**
     * Set FaultCode
     *
     * @access  public
     * @param   int faultCode
     */
    function setFaultCode($faultCode) {
      $this->faultCode= $faultCode;
    }

    /**
     * Get FaultCode
     *
     * @access  public
     * @return  int
     */
    function getFaultCode() {
      return $this->faultCode;
    }

    /**
     * Set FaultString
     *
     * @access  public
     * @param   string faultString
     */
    function setFaultString($faultString) {
      $this->faultString= $faultString;
    }

    /**
     * Get FaultString
     *
     * @access  public
     * @return  string
     */
    function getFaultString() {
      return $this->faultString;
    }
  }
?>
