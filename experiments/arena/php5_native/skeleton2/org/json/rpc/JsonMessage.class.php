<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.rpc.AbstractRpcMessage',
    'org.json.JsonFactory'
  );

  /**
   * Json Message
   *
   * @see       http://json-rpc.org/wiki/specification
   * @purpose   Contains the Json message
   */
  class JsonMessage extends Object implements AbstractRpcMessage {
    public
      $method   = '',
      $id       = '',
      $encoding = 'iso-8859-1',
      $data     = NULL,
      $class    = '';

    /**
     * Create message from string representation
     *
     * @model   abstract
     * @access  public
     * @param   string string
     * @return  &org.json.rpc.JsonMessage
     */
    public function &fromString($string) { }
    
    /**
     * Create message 
     *
     * @model   abstract
     * @access  public
     * @param   org.json.rpc.JsonMessage msg
     */
    public function create() { }
    
    /**
     * Retrieve content type for request
     *
     * @access  public
     * @return  string
     */
    public function getContentType() {
      return 'application/json';
    }    

    /**
     * Set Method
     *
     * @access  public
     * @param   string method
     */
    public function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Get Method
     *
     * @access  public
     * @return  string
     */
    public function getMethod() {
      return $this->method;
    }

    /**
     * Set Encoding
     *
     * @access  public
     * @param   string encoding
     */
    public function setEncoding($encoding) {
      $this->encoding= $encoding;
    }

    /**
     * Get Encoding
     *
     * @access  public
     * @return  string
     */
    public function getEncoding() {
      return $this->encoding;
    }

    /**
     * Set Data
     *
     * @access  public
     * @param   &lang.Object data
     */
    public function setData($data) { }

    /**
     * Get Data
     *
     * @access  public
     * @return  &lang.Object
     */
    public function &getData() {
      return $this->data;
    }
    
    /**
     * Retrieve serialized representation
     *
     * @access  public
     * @return  string
     */
    public function serializeData() {
      $decoder= &JsonFactory::create();
      return $decoder->encode($this->data);
    }
    
    /**
     * Set Class
     *
     * @access  public
     * @param   string class
     */
    public function setHandlerClass($class) {
      $this->class= $class;
    }

    /**
     * Get Class
     *
     * @access  public
     * @return  string
     */
    public function getHandlerClass() {
      return $this->class;
    }
    
    /**
     * Set fault
     *
     * @access  public
     * @param   string faultCode
     * @param   string faultString
     */
    public function setFault($faultCode, $faultString) {
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
    public function &getFault() {
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
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @access  public
     * @return  string
     */
    public function getId() {
      return $this->id;
    }
  } 
?>
