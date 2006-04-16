<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractRpcRequest.class.php 6696 2006-03-24 15:56:09Z kiesel $ 
 */

  uses('scriptlet.rpc.AbstractRpcRequest');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
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
