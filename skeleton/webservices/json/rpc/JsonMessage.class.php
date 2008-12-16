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
  class JsonMessage extends Object implements AbstractRpcMessage {
    public
      $method   = '',
      $id       = '',
      $encoding = 'UTF-8',
      $data     = NULL,
      $class    = '';

    /**
     * Create message from string representation
     *
     * @param   string string
     * @return  webservices.json.rpc.JsonMessage
     */
    public static function fromString($string) { }
    
    /**
     * Create message 
     *
     * @param   webservices.json.rpc.JsonMessage msg
     */
    public function create() { }
    
    /**
     * Retrieve content type for request
     *
     * @return  string
     */
    public function getContentType() {
      return 'application/json';
    }    

    /**
     * Set Method
     *
     * @param   string method
     */
    public function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Get Method
     *
     * @return  string
     */
    public function getMethod() {
      return $this->method;
    }

    /**
     * Set Encoding
     *
     * @param   string encoding
     */
    public function setEncoding($encoding) {
      $this->encoding= $encoding;
    }

    /**
     * Get Encoding
     *
     * @return  string
     */
    public function getEncoding() {
      return $this->encoding;
    }

    /**
     * Set Data
     *
     * @param   lang.Object data
     */
    public function setData($data) { }

    /**
     * Get Data
     *
     * @return  lang.Object
     */
    public function getData() {
      return $this->data;
    }
    
    /**
     * Retrieve serialized representation
     *
     * @return  string
     */
    public function serializeData() {
      $decoder= JsonFactory::create();
      return $decoder->encode($this->data);
    }
    
    /**
     * Set Class
     *
     * @param   string class
     */
    public function setHandlerClass($class) {
      $this->class= $class;
    }

    /**
     * Get Class
     *
     * @return  string
     */
    public function getHandlerClass() {
      return $this->class;
    }
    
    /**
     * Set fault
     *
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
     * @return  scriptlet.rpc.RpcFault
     */
    public function getFault() {
      if (empty($this->data['error'])) return NULL;
      return new RpcFault(
        $this->data['error']['faultCode'],
        $this->data['error']['faultString']
      );
    }        

    /**
     * Set Id
     *
     * @param   string id
     */
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @return  string
     */
    public function getId() {
      return $this->id;
    }
  } 
?>
