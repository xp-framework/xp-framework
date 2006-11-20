<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Abstract base class for all other transports
   *
   * @purpose  SOAP Transport
   * @see      xp://webservices.soap.transport.SOAPHTTPTransport
   */
  class SOAPTransport extends Object {
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
     * Send the message
     *
     * @access  public
     * @param   &webservices.soap.SOAPMessage message
     */
    function send(&$message) { }
   
    /**
     * Retrieve the answer
     *
     * @access  public
     * @return  &webservices.soap.SOAPMessage
     */
    function &retrieve(&$response) { }
  }
?>
