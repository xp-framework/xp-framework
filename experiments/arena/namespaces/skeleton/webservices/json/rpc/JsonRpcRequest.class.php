<?php
/* This class is part of the XP framework
 *
 * $Id: JsonRpcRequest.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace webservices::json::rpc;
 
  uses(
    'scriptlet.rpc.AbstractRpcRequest',
    'webservices.json.rpc.JsonRequestMessage'
  );
  
  /**
   * Wraps Json Rpc Router request
   *
   * @see xp://scriptlet.rpc.AbstractRpcRequest
   */
  class JsonRpcRequest extends scriptlet::rpc::AbstractRpcRequest {
  
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
