<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.codehaus.stomp.frame.Frame');

  $package= 'org.codehaus.stomp.frame';
  class org·codehaus·stomp·frame·SendFrame extends org·codehaus·stomp·frame·Frame {

    public function __construct($destination, $data= NULL) {
      $this->setDestination($destination);
      $this->setBody($data);
    }

    public function setDestination($destination) {
      $this->addHeader('destination', $destination);
    }

    public function getDestination() {
      return $this->getHeader('destination');
    }

    public function setTransaction($name) {
      $this->addHeader('transaction', $name);
    }

    public function getTransaction() {
      return $this->getHeader('transaction');
    }

    public function setBody($data) {
      parent::setBody($data);
      $this->addHeader('content-length', strlen($this->body));
    }

    public function command() {
      return 'SEND';
    }
  }
?>
