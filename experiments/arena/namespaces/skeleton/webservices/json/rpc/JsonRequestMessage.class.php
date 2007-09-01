<?php
/* This class is part of the XP framework
 *
 * $Id: JsonRequestMessage.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace webservices::json::rpc;

  uses('webservices.json.rpc.JsonMessage');

  /**
   * JSON request message
   *
   * @see      http://json-rpc.org
   * @purpose  Wrap JSON request message
   */
  class JsonRequestMessage extends JsonMessage {
  
    /**
     * Create message from string representation
     *
     * @param   string string
     * @return  webservices.json.rpc.JsonRequestMessage
     */
    public static function fromString($string) {
      $decoder= webservices::json::JsonFactory::create();

      $msg= new ();
      $data= $decoder->decode($string);

      $msg->data= $data;
      $msg->id= $data['id'];
      
      list($cn, $method)= explode('.', $data['method']);
      $msg->setHandlerClass($cn);
      $msg->setMethod($method);
      
      return $msg;
    }
    
    /**
     * Create new message
     *
     * @param   string method
     * @param   int id
     */
    public function create($method= , $id= ) {
      $this->method= $method;
      $this->id= $id;
    }
    
    /**
     * Set the data for the message
     *
     * @param   mixed data
     */
    public function setData($data) {
      $this->data= array(
        'method'  => $this->method,
        'params'  => (array)$data,
        'id'      => $this->id
      );
    }
    
    /**
     * Get data
     *
     * @return  mixed
     */
    public function getData() {
      return $this->data['params'];
    }    
  }
?>
