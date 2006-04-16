<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractRpcResponse.class.php 6701 2006-03-27 17:27:39Z kiesel $ 
 */

  uses('scriptlet.rpc.AbstractRpcResponse');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
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
