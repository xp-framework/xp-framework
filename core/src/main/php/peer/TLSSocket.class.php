<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.CryptoSocket');

  /**
   * TLS socket
   *
   * @ext      openssl
   * @purpose  Specialized socket
   */
  class TLSSocket extends CryptoSocket {

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
