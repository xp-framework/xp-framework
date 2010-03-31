<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.codehaus.stomp.frame.Frame');

  $package= 'org.codehaus.stomp.frame';
  class org·codehaus·stomp·frame·CommitFrame extends org·codehaus·stomp·frame·Frame {

    public function __construct($txname) {
      $this->setTransaction($txname);
    }

    public function command() {
      return 'COMMIT';
    }

    public function setTransaction($name) {
      $this->addHeader('transaction', $name);
    }

    public function getTransaction() {
      $this->getHeader('transaction');
    }
  }
?>
