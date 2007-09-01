<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractRpcMessage.class.php 9112 2007-01-04 11:53:10Z kiesel $ 
 */

  namespace scriptlet::rpc;

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
     * @param   string string
     * @return  scriptlet.rpc.AbstractRpcMessage
     */
    public static function fromString($string);
    
    /**
     * Create message
     *
     */
    public function create();

    /**
     * Set Encoding
     *
     * @param   string encoding
     */
    public function setEncoding($encoding);

    /**
     * Get Encoding
     *
     * @return  string
     */
    public function getEncoding();
    
    /**
     * Retrieve Content-type for requests
     *
     * @return  string
     */
    public function getContentType();    
    
    /**
     * Set Data
     *
     * @param   lang.Object data
     */
    public function setData($data);

    /**
     * Get Data
     *
     * @return  lang.Object
     */
    public function getData();
    
    /**
     * Retrieve string representation of message as used in the
     * protocol.
     *
     * @return  string
     */
    public function serializeData();
    
    /**
     * Set Class
     *
     * @param   string class
     */
    public function setHandlerClass($class);

    /**
     * Get Class
     *
     * @return  string
     */
    public function getHandlerClass();

    /**
     * Set Method
     *
     * @param   string method
     */
    public function setMethod($method);

    /**
     * Get Method
     *
     * @return  string
     */
    public function getMethod();
    
    /**
     * Set fault
     *
     */
    public function setFault($faultCode, $faultString);
    
    /**
     * Get fault
     *
     * @return  scriptlet.rpc.RpcFault
     */
    public function getFault();        
  }
?>
