<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'scriptlet.rpc.AbstractRpcRequest',
    'xml.xmlrpc.XmlRpcRequestMessage'
  );
  
  /**
   * Wraps XMl-RPC Rpc Router request
   *
   * @see xml.xmlrpc.rpc.XmlRpcRouter
   * @see scriptlet.HttpScriptletRequest
   */
  class XmlRpcRequest extends AbstractRpcRequest {
  
    /**
     * Retrieve XML-RPC message from request
     *
     * @access  public
     * @return  &xml.xmlrpc.XmlRpcMessage message object
     */
    function &getMessage() {
      $this->cat && $this->cat->debug('<<< ', $this->getData());
      $m= &XmlRpcRequestMessage::fromString($this->getData());
      return $m;
    }
  }
?>
