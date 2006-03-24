<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('scriptlet.rpc.AbstractRpcResponse');
  
  /**
   * Wraps JSON response
   *
   * @see scriptlet.HttpScriptletResponse  
   */
  class JsonRpcResponse extends AbstractRpcResponse {
    
    /**
     * Sets XML-RPC message
     *
     * @access  public
     * @param   &xml.xmlrpc.XmlRpcMessage msg XmlRpcMessage object
     */
    function setMessage(&$msg) {
      $this->message= &$msg;
    }
    
    /**
     * Make sure a fault is passed as "500 Internal Server Error"
     *
     * @access  public
     * @see     scriptlet.HttpScriptletResponse#process
     */
    function process() {
      if (!$this->message) return;

      $this->setHeader('Content-type', 'text/xml');
      if (NULL !== $this->message->getFault()) {
        $this->setStatus(HTTP_INTERNAL_SERVER_ERROR);
      }
      
      $json= &JsonFactory::create();
      $this->content= $json->encode($this->message->getData());
      $this->cat && $this->cat->debug('>>> ', $this->content);
    }
  }
?>
