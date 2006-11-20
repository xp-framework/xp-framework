<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.rpc.AbstractRpcMessage',
    'webservices.json.JsonFactory'
  );

  /**
   * Json Message
   *
   * @see       http://json-rpc.org/wiki/specification
   * @purpose   Contains the Json message
   */
  class JsonMessage extends Object {
    var
      $method   = '',
      $id       = '',
      $encoding = 'iso-8859-1',
      $data     = NULL,
      $class    = '',
      $method   = '';

    /**
     * Create message from string representation
     *
     * @model   abstract
     * @access  public
     * @param   string string
     * @return  &webservices.json.rpc.JsonMessage
     */
    function &fromString($string) { }
    
    /**
     * Create message 
     *
     * @model   abstract
     * @access  public
     * @param   webservices.json.rpc.JsonMessage msg
     */
    function create() { }
    
    /**
     * Retrieve content type for request
     *
     * @access  public
     * @return  string
     */
    function getContentType() {
      return 'application/json';
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
    function setData($data) { }

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
     * Retrieve serialized representation
     *
     * @access  public
     * @return  string
     */
    function serializeData() {
      $decoder= &JsonFactory::create();
      return $decoder->encode($this->data);
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
     * Set fault
     *
     * @access  public
     * @param   string faultCode
     * @param   string faultString
     */
    function setFault($faultCode, $faultString) {
      $this->data= array(
        'result'  => FALSE,
        'error'   => array(
          'faultCode'   => $faultCode,
          'faultString' => $faultString
        ),
        'id'      => NULL
      );
    }
    
    /**
     * Get fault
     *
     * @access  public
     * @return  &scriptlet.rpc.RpcFault
     */
    function &getFault() {
      if (empty($this->data['error'])) return NULL;
      return new RpcFault(
        $this->data['error']['faultCode'],
        $this->data['error']['faultString']
      );
    }        

    /**
     * Set Id
     *
     * @access  public
     * @param   string id
     */
    function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @access  public
     * @return  string
     */
    function getId() {
      return $this->id;
    }
  } implements(__FILE__, 'scriptlet.rpc.AbstractRpcMessage');
?>
