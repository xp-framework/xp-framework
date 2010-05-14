<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.codehaus.stomp.frame.Frame');

  $package= 'org.codehaus.stomp.frame';

  /**
   * Send frame
   *
   * @test  xp://org.codehaus.stomp.unittest.StompSendFrameTest
   */
  class org·codehaus·stomp·frame·SendFrame extends org·codehaus·stomp·frame·Frame {

    /**
     * Constructor
     *
     * @param   string destination
     * @param   string data default NULL
     * @param   array<string, string> headers default array
     */
    public function __construct($destination, $data= NULL, $headers= array()) {
      $this->headers= $headers;
      $this->setDestination($destination);
      $this->setBody($data);
    }

    /**
     * Set destination
     *
     * @param   string destination
     */
    public function setDestination($destination) {
      $this->addHeader('destination', $destination);
    }

    /**
     * Get destination
     *
     */
    public function getDestination() {
      return $this->getHeader('destination');
    }

    /**
     * Set transaction
     *
     * @param   string name
     */
    public function setTransaction($name) {
      $this->addHeader('transaction', $name);
    }

    /**
     * Get transaction
     *
     * @return  string
     */
    public function getTransaction() {
      return $this->getHeader('transaction');
    }

    /**
     * Set body
     *
     * @param   string data
     */
    public function setBody($data) {
      parent::setBody($data);
      if ($this->hasHeader('content-length')) {
        $this->addHeader('content-length', strlen($this->body));
      }
    }

    /**
     * Frame command
     *
     */
    public function command() {
      return 'SEND';
    }
  }
?>
