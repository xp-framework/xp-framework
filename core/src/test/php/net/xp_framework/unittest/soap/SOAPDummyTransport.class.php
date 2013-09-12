<?php namespace net\xp_framework\unittest\soap;

use webservices\soap\transport\SOAPHTTPTransport;
use webservices\soap\xp\XPSoapMessage;


/**
 * Dummy class for faked SOAP requests
 *
 * @purpose  Dummy SOAP Transport
 * @see      xp://webservices.soap.transport.SOAPHTTPTransport
 */
class SOAPDummyTransport extends SOAPHTTPTransport {
  public
    $answer=    '',
    $request=   null;

  /**
   * Constructor
   *
   */
  public function __construct() {
    parent::__construct('http://dummy/');
  }

  /**
   * Set Request
   *
   * @param   &webservices.soap.xp.XPSoapMessage request
   */
  public function setRequest($request) {
    $this->request= $request;
  }

  /**
   * Get Request
   *
   * @return  &webservices.soap.SOAPMessage
   */
  public function getRequest() {
    return $this->request;
  }

  /**
   * Retrieve request string
   *
   * @return  string
   */
  public function getRequestString() {
    return $this->request->getSource(0);
  }

  /**
   * Set Answer
   *
   * @param   string answer
   */
  public function setAnswer($answer) {
    $this->answer= $answer;
  }

  /**
   * Get Answer
   *
   * @return  string
   */
  public function getAnswer() {
    return $this->answer;
  }
  
  /**
   * Send the message
   *
   * @param   &webservices.soap.xp.XPSoapMessage message
   */
  public function send($message) {
    $this->request= $message; // Intentional copy
    return true;
  }    
    
  /**
   * Retrieve the answer
   *
   * @return  &webservices.soap.XPSoapMessage
   */
  public function retrieve($response) {
    return XPSoapMessage::fromString($this->answer);
  }
}
