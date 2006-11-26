<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.soap.transport.SOAPTransport');

  /**
   * Dummy class for faked SOAP requests
   *
   * @purpose  Dummy SOAP Transport
   * @see      xp://webservices.soap.transport.SOAPHTTPTransport
   */
  class SOAPDummyTransport extends SOAPTransport {
    public
      $answer=    '',
      $request=   NULL;

    /**
     * Set Request
     *
     * @access  public
     * @param   &webservices.soap.SOAPMessage request
     */
    public function setRequest(&$request) {
      $this->request= &$request;
    }

    /**
     * Get Request
     *
     * @access  public
     * @return  &webservices.soap.SOAPMessage
     */
    public function &getRequest() {
      return $this->request;
    }

    /**
     * Retrieve request string
     *
     * @access  public
     * @return  string
     */
    public function getRequestString() {
      return $this->request->getSource(0);
    }

    /**
     * Set Answer
     *
     * @access  public
     * @param   string answer
     */
    public function setAnswer($answer) {
      $this->answer= $answer;
    }

    /**
     * Get Answer
     *
     * @access  public
     * @return  string
     */
    public function getAnswer() {
      return $this->answer;
    }
    
    /**
     * Send the message
     *
     * @access  public
     * @param   &webservices.soap.SOAPMessage message
     */
    public function send(&$message) {
      $this->request= $message; // Intentional copy
      return TRUE;
    }    
      
    /**
     * Retrieve the answer
     *
     * @access  public
     * @return  &webservices.soap.SOAPMessage
     */
    public function &retrieve() {
      return SOAPMessage::fromString($this->answer);
    }
  }
?>
