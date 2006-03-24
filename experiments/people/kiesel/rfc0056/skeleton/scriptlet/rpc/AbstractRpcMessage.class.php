<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Rpc Message interface
   *
   * @purpose  RpcMessage interface
   */
  class AbstractRpcMessage extends Interface {

    /**
     * Create message from string
     *
     * @model   static
     * @access  public
     * @param   string string
     * @return  &scriptlet.rpc.AbstractRpcMessage
     */
    function fromString($string) {}
    
    /**
     * Create message
     *
     * @access  public
     */
    function create() {}

    /**
     * Set Encoding
     *
     * @access  public
     * @param   string encoding
     */
    function setEncoding($encoding) {}

    /**
     * Get Encoding
     *
     * @access  public
     * @return  string
     */
    function getEncoding() {}
    
    /**
     * Set Data
     *
     * @access  public
     * @param   &lang.Object data
     */
    function setData(&$data) {}

    /**
     * Get Data
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getData() {}
    
    /**
     * Set Class
     *
     * @access  public
     * @param   string class
     */
    function setClass($class) {}

    /**
     * Get Class
     *
     * @access  public
     * @return  string
     */
    function getClass() {}

    /**
     * Set Method
     *
     * @access  public
     * @param   string method
     */
    function setMethod($method) {}

    /**
     * Get Method
     *
     * @access  public
     * @return  string
     */
    function getMethod() {}
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setFault() {}
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getFault() {}        
  }
?>
