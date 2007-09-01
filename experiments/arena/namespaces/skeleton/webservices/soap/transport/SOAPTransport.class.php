<?php
/* This class is part of the XP framework
 *
 * $Id: SOAPTransport.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace webservices::soap::transport;

  /**
   * Abstract base class for all other transports
   *
   * @purpose  SOAP Transport
   * @see      xp://webservices.soap.transport.SOAPHTTPTransport
   */
  class SOAPTransport extends lang::Object {
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
     * Send the message
     *
     * @param   webservices.soap.SOAPMessage message
     */
    public function send($message) { }
   
    /**
     * Retrieve the answer
     *
     * @return  webservices.soap.SOAPMessage
     */
    public function retrieve($response) { }
  }
?>
