<?php
/* This class is part of the XP framework
 *
 * $Id: XmlRpcRequest.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace webservices::xmlrpc::rpc;
 
  uses(
    'scriptlet.rpc.AbstractRpcRequest',
    'webservices.xmlrpc.XmlRpcRequestMessage'
  );
  
  /**
   * Wraps XMl-RPC Rpc Router request
   *
   * @see webservices.xmlrpc.rpc.XmlRpcRouter
   * @see scriptlet.HttpScriptletRequest
   */
  class XmlRpcRequest extends scriptlet::rpc::AbstractRpcRequest {
  
    /**
     * Retrieve XML-RPC message from request
     *
     * @return  webservices.xmlrpc.XmlRpcMessage message object
     */
    public function getMessage() {
      $this->cat && $this->cat->debug('<<< ', $this->getData());
      $m= webservices::xmlrpc::XmlRpcRequestMessage::fromString($this->getData());
      return $m;
    }
  }
?>
