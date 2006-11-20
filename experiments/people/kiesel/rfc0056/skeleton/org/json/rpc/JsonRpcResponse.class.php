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
     * Sets JSON message
     *
     * @access  public
     * @param   &webservices.json.rpc.JsonRpcMessage msg
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

      if (NULL !== $this->message->getFault()) {
        $this->setStatus(HTTP_INTERNAL_SERVER_ERROR);
      }
      
      $this->content= $this->message->serializeData();
      $this->cat && $this->cat->debug('>>> ', $this->content);
    }
  }
?>
