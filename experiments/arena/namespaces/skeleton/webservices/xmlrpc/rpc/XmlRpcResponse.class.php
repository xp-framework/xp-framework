<?php
/* This class is part of the XP framework
 *
 * $Id: XmlRpcResponse.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace webservices::xmlrpc::rpc;
 
  uses('scriptlet.rpc.AbstractRpcResponse');
  
  /**
   * Wraps XML-RPC response
   *
   * @see scriptlet.HttpScriptletResponse  
   */
  class XmlRpcResponse extends scriptlet::rpc::AbstractRpcResponse {
    
    /**
     * Make sure a fault is passed as "500 Internal Server Error"
     *
     * @see     scriptlet.HttpScriptletResponse#process
     */
    public function process() {
      if (!$this->message) return;

      if (NULL !== $this->message->getFault()) {
        $this->setStatus(HTTP_INTERNAL_SERVER_ERROR);
      }
      $this->content= $this->message->serializeData();
      $this->cat && $this->cat->debug('>>> ', $this->content);
    }
  }
?>
