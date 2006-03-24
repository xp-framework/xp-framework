<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.HttpScriptletRequest');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AbstractRpcRequest extends HttpScriptletRequest {
    var
      $cat      = NULL;
    
    /**
     * Set trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) {
      $this->cat= &$cat;
    }
  
    /**
     * Create message from request
     *
     * @model   abstract
     * @access  public
     * @return  &scriptlet.rpc.AbstractRpcMessage
     */
    function getMessage() {
      // Override this. You must set the 'class' and 'method' members of
      // the respective RpcMessage class.
    }
    
    /**
     * Determine encoding.
     *
     * @access  public
     * @return  string
     */
    function getEncoding() {
      // Figure out encoding if given
      $type= $this->getHeader('Content-type');
      if (FALSE !== ($pos= strpos($type, 'charset='))) {
        return substr($type, $pos+ 8);
      }
      
      return 'iso-8859-1';
    }
  } implements(__FILE__, 'util.log.Traceable');
?>
