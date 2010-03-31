<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.codehaus.stomp.frame.Frame');

  $package= 'org.codehaus.stomp.frame';
  class org·codehaus·stomp·frame·UnsubscribeFrame extends org·codehaus·stomp·frame·Frame {

    public function __construct($queue, $id= NULL) {
      if (NULL === $queue && NULL === $id) throw new IllegalArgumentException(
        'Either destination or id must be given.'
      );

      if (NULL !== $queue) {
        $this->setDestination($queue);
      } else {
        $this->setId($id);
      }
    }

    public function command() {
      return 'UNSUBSCRIBE';
    }

    public function setDestination($destination) {
      $this->addHeader('destination', $destination);
    }

    public function getDestination() {
      return $this->getHeader('destination');
    }

    public function setId($id) {
      $this->addHeader('id', $id);
    }

    public function getId() {
      return $this->getHeader('id');
    }
  }
?>
