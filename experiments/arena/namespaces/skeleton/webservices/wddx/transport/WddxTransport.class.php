<?php
/* This class is part of the XP framework
 *
 * $Id: WddxTransport.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace webservices::wddx::transport;

  uses('util.log.Traceable');

  /**
   * Base class for WDDX transports.
   *
   * @purpose  Base class.
   */
  class WddxTransport extends lang::Object implements util::log::Traceable {
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
