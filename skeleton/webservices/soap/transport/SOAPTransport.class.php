<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Abstract base class for all other transports
   *
   * @purpose  SOAP Transport
   * @see      xp://xml.soap.transport.SOAPHTTPTransport
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
     * @param   &xml.soap.SOAPMessage message
     */
    function send(&$message) { }
   
    /**
     * Retrieve the answer
     *
     * @access  public
     * @return  &xml.soap.SOAPMessage
     */
    function &retrieve(&$response) { }
  }
?>
