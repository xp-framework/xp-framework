<?php
/* This class is part of the XP framework
 *
 * $Id: GenericRpcResponse.class.php 7447 2006-07-21 16:15:49Z kiesel $ 
 */

  uses('scriptlet.rpc.AbstractRpcResponse');

  /**
   * Generic RPC response.
   *
   * Instead of deriving the AbstractRpcResponse in every new
   * implementation, you can use this generic RPC response which
   * must be given a callback class (usually the RpcRouter) which
   * then can execute the actions.
   *
   * @see      xp://scriptlet.rpc.AbstractRpcRouter
   * @purpose  Generic RPC response
   */
  class GenericRpcResponse extends AbstractRpcResponse {
    public
      $_cb=   NULL;
    
    /**
     * Set callback object.
     *
     * @param   var object
     */
    public function setCallback($object) {
      $this->_cb= $object;
    }
  
    /**
     * Process response. Sets the headers and response body
     * of the response.
     *
     * GenericRpcResponse delegates this to the callback object
     * (usually the Router).
     *
     * @see     scriptlet.HttpScriptletResponse#process
     */
    public function process() {
      return $this->_cb->setResponse($this);
    }
  }
?>
