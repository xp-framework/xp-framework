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
      $answer=    '',
      $request=   NULL;

    /**
     * Set Request
     *
     * @access  public
     * @param   &xml.soap.SOAPMessage request
     */
    function setRequest(&$request) {
      $this->request= &$request;
    }

    /**
     * Get Request
     *
     * @access  public
     * @return  &xml.soap.SOAPMessage
     */
    function &getRequest() {
      return $this->request;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getRequestString() {
      return $this->request->getSource(0);
    }

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
      $this->request= $message; // Intentional copy
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
