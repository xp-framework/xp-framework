<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Socket');

  /**
   * SSL (Secure socket layer) socket
   *
   * Will attempt to negotiate an SSL V2 or SSL V3 connection depending
   * on the capabilities and preferences of the remote host if the SSL
   * version is omitted. The constructor's version parameter can be used
   * to explicitely select a specific SSL version by passing 2 or 3.
   *
   * @see   php://transports
   * @ext   openssl
   */
  class SSLSocket extends Socket {

    /**
     * Constructor
     *
     * @param   string host hostname or IP address
     * @param   int port
     * @param   resource socket default NULL
     * @param   int version default NULL
     */
    public function __construct($host, $port, $socket= NULL, $version= NULL) {
      parent::__construct($host, $port, $socket);
      $this->_prefix= 'ssl'.($version ? 'v'.$version : '').'://';
    }
  }
?>
