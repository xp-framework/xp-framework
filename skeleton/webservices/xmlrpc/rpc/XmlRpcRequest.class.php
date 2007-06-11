<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
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
  class XmlRpcRequest extends AbstractRpcRequest {
  
    /**
     * Retrieve XML-RPC message from request
     *
     * @return  webservices.xmlrpc.XmlRpcMessage message object
     */
    public function getMessage() {
      $this->cat && $this->cat->debug('<<< ', $this->getData());
      $m= XmlRpcRequestMessage::fromString($this->getData());
      return $m;
    }
  }
?>
