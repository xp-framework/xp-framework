<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.HttpScriptletRequest', 'util.log.Traceable');

  /**
   * RPC request
   *
   * @see      xp://scriptlet.rpc.AbstractRpcRouter
   * @purpose  Rquest
   */
  class AbstractRpcRequest extends HttpScriptletRequest implements Traceable {
    public
      $cat      = NULL;
    
    /**
     * Set trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
  
    /**
     * Create message from request
     *
     * @return  scriptlet.rpc.AbstractRpcMessage
     */
    public function getMessage() {
      // Override this. You must set the 'class' and 'method' members of
      // the respective RpcMessage class.
    }
    
    /**
     * Determine encoding.
     *
     * @return  string
     */
    public function getEncoding() {
      // Figure out encoding if given
      $type= $this->getHeader('Content-type');
      if (FALSE !== ($pos= strpos($type, 'charset='))) {
        return substr($type, $pos+ 8);
      }
      
      return 'iso-8859-1';
    }
  } 
?>
