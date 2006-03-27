<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('scriptlet.rpc.AbstractRpcResponse');
  
  /**
   * Wraps XML-RPC response
   *
   * @see scriptlet.HttpScriptletResponse  
   */
  class XmlRpcResponse extends AbstractRpcResponse {
    
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
      $this->content= (
        $this->message->getDeclaration()."\n".
        $this->message->getSource(0)
      );
      
      $this->cat && $this->cat->debug('>>> ', $this->content);
    }
  }
?>
