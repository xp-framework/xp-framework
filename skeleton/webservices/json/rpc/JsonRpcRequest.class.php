<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'scriptlet.rpc.AbstractRpcRequest',
    'webservices.json.rpc.JsonRequestMessage'
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
     * @return  webservices.xmlrpc.XmlRpcMessage message object
     */
    public function getMessage() {
      $this->cat && $this->cat->debug('<<< ', $this->getData());
      $m= JsonRequestMessage::fromString($this->getData());
      return $m;
    }
  }
?>
