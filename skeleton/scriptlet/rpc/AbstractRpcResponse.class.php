<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.HttpScriptletResponse');

  /**
   * RPC response object
   *
   * @see      xp://scriptlet.rpc.AbstractRpcRouter
   * @purpose  Response
   */
  class AbstractRpcResponse extends HttpScriptletResponse {
    var
      $message  = NULL;
    
    var
      $cat      = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->setHeader('Server', 'Abstract RPC 1.0 / PHP'.phpversion().' / XP Framework');
    }
    
    /**
     * Sets message object
     *
     * @access  public
     * @param   scriptlet.rpc.AbstractRpcMessage msg
     */
    function setMessage($msg) {
      $this->message= &$msg;
    }
    
    /**
     * Set trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) {
      $this->cat= &$cat;
    }
  } implements(__FILE__, 'util.log.Traceable');
?>
