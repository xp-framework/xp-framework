<?php
/* This class is part of the XP framework
 *
 * $Id: SSLSocket.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace peer;

  ::uses('peer.Socket');

  /**
   * SSL (Secure socket layer) socket
   *
   * @ext      openssl
   * @purpose  Specialized socket
   */
  class SSLSocket extends Socket {

    /**
     * Constructor
     *
     * @param   string host hostname or IP address
     * @param   int port
     * @param   resource socket default NULL
     */
    public function __construct($host, $port, $socket= NULL) {
      parent::__construct($host, $port, $socket);
      $this->_prefix= 'ssl://';
    }
  }
?>
