<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'scriptlet.rpc.AbstractRpcRequest',
    'org.json.rpc.JsonMessage'
  );
  
  /**
   * Wraps Json Rpc Router request
   *
   * @see xp://scriptlet.rpc.AbstractRpcRequest
   */
  class JsonRpcRequest extends AbstractRpcRequest {
  
    /**
     * Retrieve Json message from request
     *
     * @access  public
     * @return  &xml.xmlrpc.XmlRpcMessage message object
     */
    function &getMessage() {
      $this->cat && $this->cat->debug('<<< ', $this->getData());
      $m= &JsonMessage::fromString($this->getData());
      
      $data= $m->getData();
      list($class, $method)= explode('.', $data->method);
      $m->setClass($class);
      $m->setMethod($method);
      
      return $m;
    }
  }
?>
