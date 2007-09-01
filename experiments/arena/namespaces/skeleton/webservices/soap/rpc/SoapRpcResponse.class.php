<?php
/* This class is part of the XP framework
 *
 * $Id: SoapRpcResponse.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace webservices::soap::rpc;
 
  uses('scriptlet.rpc.AbstractRpcResponse');
  
  /**
   * Wraps SOAP response
   *
   * @see scriptlet.HttpScriptletResponse  
   */
  class SoapRpcResponse extends scriptlet::rpc::AbstractRpcResponse {
    
    /**
     * Make sure a fault is passed as "500 Internal Server Error"
     *
     * @see     scriptlet.HttpScriptletResponse#process
     */
    public function process() {
      if (!$this->message) return;

      $this->setHeader('Content-type', 'text/xml');      
      if (NULL !== $this->message->getFault()) {
        $this->setStatus(HTTP_INTERNAL_SERVER_ERROR);
      }
      
      $this->content= $this->message->serializeData();
    }
  }
?>
