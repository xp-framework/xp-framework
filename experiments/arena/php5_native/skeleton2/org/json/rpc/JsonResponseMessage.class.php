<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.json.rpc.JsonMessage');

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
     * @model   static
     * @access  public
     * @param   string string
     * @return  &org.json.rpc.JsonResponseMessage
     */
    public function &fromString($string) {
      $decoder= &JsonFactory::create();

      $msg= &new JsonResponseMessage();
      $data= $decoder->decode($string);

      $msg->data= $data;
      $msg->id= $data['id'];
      return $msg;
    }
    
    /**
     * Create new message
     *
     * @access  public
     * @param   string method
     * @param   int id
     */
    public function create($msg) {
      $this->id= $msg->getId();
    }
    
    /**
     * Set the data for the message
     *
     * @access  public
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
     * @access  public
     * @return  mixed
     */
    public function getData() {
      return $this->data['result'];
    }    
    
    /**
     * Set a fault for the message
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
        'id'      => $this->id
      );
    }    
  }
?>
