<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.log.Traceable');

  /**
   * Base class for RPC transports.
   *
   * @purpose  Base class.
   */
  class AbstractRpcTransport extends Object implements Traceable {
    public
      $cat  = NULL;
      
    /**
     * Set trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
 
    /**
     * Send XML-RPC message
     *
     * @param   scriptlet.rpc.AbstractRpcMessage message
     * @return  scriptlet.HttpScriptletResponse
     */
    public function send($message) { }
    
    /**
     * Retrieve a XML-RPC message.
     *
     * @param   scriptlet.rpc.AbstractRpcResponse response
     * @return  scriptlet.rpc.AbstractRpcMessage
     */
    public function retrieve($response) { }    

  } 
?>
