<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.rpc.RpcFault');

  /**
   * Rpc Message interface
   *
   * @purpose  RpcMessage interface
   */
  interface AbstractRpcMessage {

    /**
     * Create message from string
     *
     * @model   static
     * @access  public
     * @param   string string
     * @return  &scriptlet.rpc.AbstractRpcMessage
     */
    public function &fromString($string);
    
    /**
     * Create message
     *
     * @access  public
     */
    public function create();

    /**
     * Set Encoding
     *
     * @access  public
     * @param   string encoding
     */
    public function setEncoding($encoding);

    /**
     * Get Encoding
     *
     * @access  public
     * @return  string
     */
    public function getEncoding();
    
    /**
     * Retrieve Content-type for requests
     *
     * @access  public
     * @return  string
     */
    public function getContentType();    
    
    /**
     * Set Data
     *
     * @access  public
     * @param   &lang.Object data
     */
    public function setData(&$data);

    /**
     * Get Data
     *
     * @access  public
     * @return  &lang.Object
     */
    public function &getData();
    
    /**
     * Retrieve string representation of message as used in the
     * protocol.
     *
     * @access  public
     * @return  string
     */
    public function serializeData();
    
    /**
     * Set Class
     *
     * @access  public
     * @param   string class
     */
    public function setClass($class);

    /**
     * Get Class
     *
     * @access  public
     * @return  string
     */
    public function getClass();

    /**
     * Set Method
     *
     * @access  public
     * @param   string method
     */
    public function setMethod($method);

    /**
     * Get Method
     *
     * @access  public
     * @return  string
     */
    public function getMethod();
    
    /**
     * Set fault
     *
     * @access  public
     */
    public function setFault();
    
    /**
     * Get fault
     *
     * @access  public
     * @return  &scriptlet.rpc.RpcFault
     */
    public function &getFault();        
  }
?>
