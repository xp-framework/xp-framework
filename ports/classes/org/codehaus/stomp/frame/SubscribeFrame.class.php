<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'org.codehaus.stomp.frame';

  uses('org.codehaus.stomp.frame.Frame', 'org.codehaus.stomp.AckMode');

  /**
   * Subscribe frame
   *
   */
  class org·codehaus·stomp·frame·SubscribeFrame extends org·codehaus·stomp·frame·Frame {

    /**
     * Constructor
     *
     * @see     xp://org.codehaus.stomp.AckMode
     * @param   string queue
     * @param   string ack default 'auto'
     * @param   string selector default NULL
     */
    public function __construct($queue, $ack= AckMode::AUTO, $selector= NULL) {
      $this->setDestination($queue);
      $this->setAck($ack);
      if (NULL !== $selector) $this->setSelector($selector);
    }

    /**
     * Frame command
     *
     */
    public function command() {
      return 'SUBSCRIBE';
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
     * Set selector
     *
     * @param   string selector
     */
    public function setSelector($selector) {
      $this->addHeader('selector', $selector);
    }

    /**
     * Get selector
     *
     */
    public function getSelector() {
      return $this->getHeader('selector');
    }

    /**
     * Set ack
     *
     * @see     xp://org.codehaus.stomp.AckMode
     * @param   string ack
     */
    public function setAck($ack) {
      $this->addHeader('ack', $ack);
    }

    /**
     * Get ack
     *
     * @return  string
     */
    public function getAck() {
      return $this->getHeader($ack);
    }

    /**
     * Set id
     *
     * @param   string id
     */
    public function setId($id) {
      $this->addHeader('id', $id);
    }

    /**
     * Get id
     *
     * @return  string
     */
    public function getId() {
      return $this->getHeader('id');
    }
  }
?>
