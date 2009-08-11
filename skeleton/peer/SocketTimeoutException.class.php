<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.SocketException');

  /**
   * Indicate a timeout occurred on a socket read
   *
   * @see      xp://peer.Socket#setTimeout
   * @see      xp://peer.SocketException
   */
  class SocketTimeoutException extends SocketException {
    protected $timeout= 0.0;
    
    /**
     * Constructor
     *
     * @param   string message
     * @param   float timeout
     */
    public function __construct($message, $timeout) {
      parent::__construct($message);
      $this->timeout= $timeout;
    }

    /**
     * Get timeout
     *
     * @return  float
     */
    public function getTimeout() {
      return $this->_timeout;
    }
    
    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        'Exception %s (%s after %.3f seconds)',
        $this->getClassName(),
        $this->message,
        $this->timeout
      );
    }
  }
?>
