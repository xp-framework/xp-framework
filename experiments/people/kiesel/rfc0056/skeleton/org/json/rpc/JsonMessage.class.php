<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.rpc.AbstractRpcMessage');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class JsonMessage extends Object {
    var
      $method   = '',
      $encoding = '',
      $data     = NULL,
      $class    = '',
      $method   = '';

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &fromString($string) {
      $decoder= &JsonFactory::create();
      $msg= &new JsonMessage();
      $msg->setData($decoder->decode($string));
      return $msg;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function create() {
    
    }    

    /**
     * Set Method
     *
     * @access  public
     * @param   string method
     */
    function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Get Method
     *
     * @access  public
     * @return  string
     */
    function getMethod() {
      return $this->method;
    }

    /**
     * Set Encoding
     *
     * @access  public
     * @param   string encoding
     */
    function setEncoding($encoding) {
      $this->encoding= $encoding;
    }

    /**
     * Get Encoding
     *
     * @access  public
     * @return  string
     */
    function getEncoding() {
      return $this->encoding;
    }

    /**
     * Set Data
     *
     * @access  public
     * @param   &lang.Object data
     */
    function setData(&$data) {
      $this->data= &$data;
    }

    /**
     * Get Data
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getData() {
      return $this->data;
    }
    
    
    /**
     * Set Class
     *
     * @access  public
     * @param   string class
     */
    function setClass($class) {
      $this->class= $class;
    }

    /**
     * Get Class
     *
     * @access  public
     * @return  string
     */
    function getClass() {
      return $this->class;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setFault() {
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getFault() {
    
    }        
  } implements(__FILE__, 'scriptlet.rpc.AbstractRpcMessage');
?>
