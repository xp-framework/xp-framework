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
  class WddxTransport extends Object implements Traceable {
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
     * Send WDDX message
     *
     * @param   webservices.wddx.WddxMessage message
     * @return  scriptlet.HttpScriptletResponse
     */
    public function send($message) { }
    
    /**
     * Retrieve a WDDX message.
     *
     * @param   scriptlet.HttpScriptletResponse response
     * @return  webservices.wddx.WddxMessage
     */
    public function retrieve($response) { }
  } 
?>
