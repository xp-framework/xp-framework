<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'scriptlet.HttpScriptletRequest',
    'xml.xmlrpc.XmlRpcMessage'
  );
  
  /**
   * Wraps XMl-RPC Rpc Router request
   *
   * @see xml.xmlrpc.rpc.XmlRpcRouter
   * @see scriptlet.HttpScriptletRequest
   */
  class XmlRpcRequest extends HttpScriptletRequest {
  
    /**
     * Retrieve XML-RPC message from request
     *
     * @access  public
     * @return  &xml.xmlrpc.XmlRpcMessage message object
     */
    function &getMessage() {
    
      // TBD: Should this really be static?
      static $m;
      
      if (!isset($m)) {
        $this->cat && $this->cat->debug('<<< ', $this->getData());
        $m= &XmlRpcMessage::fromString($this->getData());
        $m->method= $m->root->children[0]->getContent();
      }
      
      return $m;
    }
  }
?>
