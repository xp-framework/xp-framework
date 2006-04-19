<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractRpcResponse.class.php 6701 2006-03-27 17:27:39Z kiesel $ 
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
    var
      $_cb=   NULL;
    
    /**
     * Set callback object.
     *
     * @access  public
     * @param   &mixed
     */
    function setCallback(&$object) {
      $this->_cb= &$object;
    }
  
    /**
     * Process response. Sets the headers and response body
     * of the response.
     *
     * GenericRpcResponse delegates this to the callback object
     * (usually the Router).
     *
     * @access  public
     * @see     scriptlet.HttpScriptletResponse#process
     */
    function process() {
      return $this->_cb->setResponse($this);
    }
  }
?>
