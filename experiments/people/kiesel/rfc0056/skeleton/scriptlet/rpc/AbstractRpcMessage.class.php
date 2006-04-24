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
    function &fromString($string) {}
    
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
     * Retrieve Content-type for requests
     *
     * @access  public
     * @return  string
     */
    function getContentType() {}    
    
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
     * Retrieve string representation of message as used in the
     * protocol.
     *
     * @access  public
     * @return  string
     */
    function serializeData() {}
    
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
     * Set fault
     *
     * @access  public
     */
    function setFault() {}
    
    /**
     * Get fault
     *
     * @access  public
     * @return  &scriptlet.rpc.RpcFault
     */
    function &getFault() {}        
  }
?>
