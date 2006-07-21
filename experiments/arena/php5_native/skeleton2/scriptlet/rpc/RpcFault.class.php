<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represent an RPC Fault.
   *
   * @purpose  Wrap fault
   */
  class RpcFault extends Object {
    public
      $faultCode=     0,
      $faultString=   '';

    /**
     * Constructor.
     *
     * @access  public
     * @param   int code
     * @param   string string
     */
    public function __construct($code, $string) {
      $this->faultCode= $code;
      $this->faultString= $string;
    }

    /**
     * Set FaultCode
     *
     * @access  public
     * @param   int faultCode
     */
    public function setFaultCode($faultCode) {
      $this->faultCode= $faultCode;
    }

    /**
     * Get FaultCode
     *
     * @access  public
     * @return  int
     */
    public function getFaultCode() {
      return $this->faultCode;
    }

    /**
     * Set FaultString
     *
     * @access  public
     * @param   string faultString
     */
    public function setFaultString($faultString) {
      $this->faultString= $faultString;
    }

    /**
     * Get FaultString
     *
     * @access  public
     * @return  string
     */
    public function getFaultString() {
      return $this->faultString;
    }
  }
?>
