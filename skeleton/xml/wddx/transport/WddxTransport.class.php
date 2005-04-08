<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.log.Traceable');

  /**
   * Base class for WDDX transports.
   *
   * @purpose  Base class.
   */
  class WddxTransport extends Object {
    var
      $cat  = NULL;
      
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
     * Send WDDX message
     *
     * @access  public
     * @param   &xml.wddx.WddxMessage message
     * @return  &scriptlet.HttpScriptletResponse
     */
    function &send(&$message) { }
    
    /**
     * Retrieve a WDDX message.
     *
     * @access  public
     * @param   &scriptlet.HttpScriptletResponse response
     * @return  &xml.wddx.WddxMessage
     */
    function &retrieve(&$response) { }
  } implements(__FILE__, 'util.log.Traceable');
?>
