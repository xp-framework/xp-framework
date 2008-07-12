<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.xmlrpc.XmlRpcMessage');

  /**
   * XmlRpc response message. 
   *
   * @ext      xml
   * @see      xp://webservices.xmlrpc.XmlRpcRequestMessage
   * @purpose  Wrap XML-RPC Response
   */
  class XmlRpcResponseMessage extends XmlRpcMessage {
  
    /**
     * Create a response message for the given request
     *
     * @param   webservices.xmlrpc.XmlRpcRequestMessage reqmsg
     */
    public function create($reqmsg= NULL) {
      $this->tree= new Tree(XMLRPC_RESPONSE);
    }
    
    /**
     * Construct a XML-RPC response message from a string
     *
     * <code>
     *   $msg= XmlRpcResponseMessage::fromString('<methodCall>...</methodCall>');
     * </code>
     *
     * @param   string string
     * @return  webservices.xmlrpc.XmlRpcResponse Message
     */
    public static function fromString($string) {
      $msg= new self();
      $msg->tree= Tree::fromString($string);

      if (!isset($msg->tree->root->children[0])) {
        throw new FormatException('Response is not well formed'); 
      }

      // Set class and method members from XML structure
      $target= $msg->tree->root->children[0]->getContent();
      @list($msg->class, $msg->method)= explode('.', $target);

      return $msg;
    }
    
    /**
     * Set the data for the message.
     *
     * @param   mixed arr
     */
    public function setData($value) {
      $encoder= new XmlRpcEncoder();

      $params= $this->tree->root->addChild(new Node('params'));
      $param= $params->addChild(new Node('param'));
      $param->addChild($encoder->encode($value));
    }
    
    /**
     * Return the data from the message.
     *
     * @return  mixed
     */
    public function getData() {
      $ret= array();
      
      if (
        !is('xml.Node', $this->tree->root->children[0]->children[0]->children[0]) ||
        'value' != $this->tree->root->children[0]->children[0]->children[0]->getName()
      ) {
        throw(new IllegalStateException('No node "params" found.'));
      }

      // Process params node
      $decoder= new XmlRpcDecoder();
      
      // Access node /methodResponse/params/param/value node
      return $decoder->decode($this->tree->root->children[0]->children[0]->children[0]);
    }
  }
?>
