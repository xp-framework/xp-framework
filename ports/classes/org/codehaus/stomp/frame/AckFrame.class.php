<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.codehaus.stomp.frame.Frame');

  $package= 'org.codehaus.stomp.frame';
  class org·codehaus·stomp·frame·AckFrame extends org·codehaus·stomp·frame·Frame {

    public function __construct($messageId, $txname= NULL) {
      $this->setMessageId($messageId);
      if (NULL !== $txname) $this->setTransaction($txname);
    }

    public function command() {
      return 'ACK';
    }

    public function setTransaction($name) {
      $this->addHeader('transaction', $name);
    }

    public function getTransaction() {
      $this->getHeader('transaction');
    }

    public function setMessageId($messageId) {
      $this->addHeader('message-id', $messageId);
    }

    public function getMessageId() {
      return $this->getHeader('message-id');
    }
  }
?>
