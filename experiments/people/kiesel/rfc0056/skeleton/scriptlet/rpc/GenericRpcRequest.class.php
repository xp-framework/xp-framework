<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractRpcRequest.class.php 6696 2006-03-24 15:56:09Z kiesel $ 
 */

  uses('scriptlet.rpc.AbstractRpcRequest');

  /**
   * Generic RPC request
   *
   * Instead of deriving the AbstractRpcRequest in every new
   * implementation, you can use this generic RPC request which
   * must be given a callback class (usually the RpcRouter) which
   * then can execute the actions.
   *
   * @see      xp://scriptlet.rpc.AbstractRpcRouter
   * @purpose  Generic RPC request
   */
  class GenericRpcRequest extends AbstractRpcRequest {
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
     * Create message from request
     *
     * @access  public
     * @return  &scriptlet.rpc.AbstractRpcMessage
     */
    function &getMessage() {
      return $this->_cb->getMessage($this);
    }
    
    /**
     * Determine encoding.
     *
     * @access  public
     * @return  string
     */
    function getEncoding() {
      if (method_exists($this->_cb, 'getEncoding')) return $this->_cb->getEncoding($this);
      return parent::getEncoding();
    }
  }
?>
