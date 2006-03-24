<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.HttpScriptletResponse');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AbstractRpcResponse extends HttpScriptletResponse {
    var
      $message  = NULL;
    
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
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->setHeader('Server', 'Abstract RPC 1.0#/PHP'.phpversion().'/ XP Framework');
    }
  } implements(__FILE__, 'util.log.Traceable');
?>
