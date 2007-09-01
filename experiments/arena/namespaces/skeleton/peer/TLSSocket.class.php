<?php
/* This class is part of the XP framework
 *
 * $Id: TLSSocket.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace peer;

  ::uses('peer.Socket');

  /**
   * TLS socket
   *
   * @ext      openssl
   * @purpose  Specialized socket
   */
  class TLSSocket extends Socket {

    /**
     * Constructor
     *
     * @param   string host hostname or IP address
     * @param   int port
     * @param   resource socket default NULL
     */
    public function __construct($host, $port, $socket= NULL) {
      parent::__construct($host, $port, $socket);
      $this->_prefix= 'tls://';
    }
  }
?>
