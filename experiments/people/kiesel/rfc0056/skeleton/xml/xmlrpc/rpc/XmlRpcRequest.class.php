<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'scriptlet.rpc.AbstractRpcRequest',
    'xml.xmlrpc.XmlRpcMessage'
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
      $m= &XmlRpcMessage::fromString($this->getData());
      
      // Determine class- and method-name
      $node= $m->root->children[0]->getContent();
      $m->setClass(substr($node, 0, strpos($node, '.')));
      $m->setMethod(substr($node, strpos($node, '.') + 1));
      
      return $m;
    }
  }
?>
