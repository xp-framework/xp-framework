<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.codehaus.stomp.frame.Frame');

  $package= 'org.codehaus.stomp.frame';
  class org·codehaus·stomp·frame·SubscribeFrame extends org·codehaus·stomp·frame·Frame {
    const
      ACK_CLIENT  = 'client',
      ACK_AUTO    = 'auto';

    public function __construct($queue, $ack= self::ACK_AUTO) {
      $this->setDestination($queue);
      $this->setAck($ack);
    }

    public function command() {
      return 'SUBSCRIBE';
    }

    public function setDestination($destination) {
      $this->addHeader('destination', $destination);
    }

    public function getDestination() {
      return $this->getHeader('destination');
    }

    public function setAck($ack) {
      $this->addHeader('ack', $ack);
    }

    public function getAck() {
      return $this->getHeader($ack);
    }

    public function setId($id) {
      $this->addHeader('id', $id);
    }

    public function getId() {
      return $this->getHeader('id');
    }
  }
?>
