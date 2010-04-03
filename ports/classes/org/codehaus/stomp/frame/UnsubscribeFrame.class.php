<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.codehaus.stomp.frame.Frame');

  $package= 'org.codehaus.stomp.frame';

  /**
   * Unsubscribe frame
   *
   */
  class org·codehaus·stomp·frame·UnsubscribeFrame extends org·codehaus·stomp·frame·Frame {

    /**
     * Constructor
     *
     * @param   string queue
     * @param   string id default NULL
     */
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

    /**
     * Frame command
     *
     */
    public function command() {
      return 'UNSUBSCRIBE';
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
