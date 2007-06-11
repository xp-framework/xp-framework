<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.json.rpc.JsonMessage');

  /**
   * JSON response message
   *
   * @see      http://json-rpc.org
   * @purpose  Wrap JSON response message
   */
  class JsonResponseMessage extends JsonMessage {
  
    /**
     * Create message from string representation
     *
     * @param   string string
     * @return  webservices.json.rpc.JsonResponseMessage
     */
    public static function fromString($string) {
      $decoder= JsonFactory::create();

      $msg= new JsonResponseMessage();
      $data= $decoder->decode($string);

      $msg->data= $data;
      $msg->id= $data['id'];
      return $msg;
    }
    
    /**
     * Create new message
     *
     * @param   string method
     * @param   int id
     */
    public function create($msg= NULL) {
      $this->id= $msg->getId();
    }
    
    /**
     * Set the data for the message
     *
     * @param   mixed data
     */
    public function setData($data) {
      $this->data= array(
        'result'  => $data,
        'error'   => NULL,
        'id'      => $this->id
      );
    }
    
    /**
     * Get data
     *
     * @return  mixed
     */
    public function getData() {
      return $this->data['result'];
    }    
    
    /**
     * Set a fault for the message
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
        'id'      => $this->id
      );
    }    
  }
?>
