<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.codehaus.stomp.frame.Frame');

  $package= 'org.codehaus.stomp.frame';

  /**
   * Abort frame
   *
   */
  class org·codehaus·stomp·frame·AbortFrame extends org·codehaus·stomp·frame·Frame {

    /**
     * Constructor
     *
     * @param   string txname
     */
    public function __construct($txname) {
      $this->setTransaction($txname);
    }

    /**
     * Retrieve frame command
     *
     */
    public function command() {
      return 'ABORT';
    }

    /**
     * Set transaction name
     *
     * @param   string name
     */
    public function setTransaction($name) {
      $this->addHeader('transaction', $name);
    }

    /**
     * Retrieve transaction name
     *
     * @return  string
     */
    public function getTransaction() {
      $this->getHeader('transaction');
    }
  }
?>
