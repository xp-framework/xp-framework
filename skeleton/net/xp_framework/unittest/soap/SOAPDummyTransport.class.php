<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.soap.transport.SOAPTransport');

  /**
   * Dummy class for faked SOAP requests
   *
   * @purpose  Dummy SOAP Transport
   * @see      xp://xml.soap.transport.SOAPHTTPTransport
   */
  class SOAPDummyTransport extends SOAPTransport {
    var
      $answer=    '';

    /**
     * Set Answer
     *
     * @access  public
     * @param   string answer
     */
    function setAnswer($answer) {
      $this->answer= $answer;
    }

    /**
     * Get Answer
     *
     * @access  public
     * @return  string
     */
    function getAnswer() {
      return $this->answer;
    }
    
    /**
     * Send the message
     *
     * @access  public
     * @param   &xml.soap.SOAPMessage message
     */
    function send(&$message) {
      return TRUE;
    }    
      
    /**
     * Retrieve the answer
     *
     * @access  public
     * @return  &xml.soap.SOAPMessage
     */
    function &retrieve() {
      return SOAPMessage::fromString($this->answer);
    }
  }
?>
