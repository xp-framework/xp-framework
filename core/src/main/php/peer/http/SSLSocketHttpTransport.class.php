<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.http.SocketHttpTransport', 'peer.SSLSocket');

  /**
   * Transport via SSL sockets
   *
   * @ext      openssl
   * @see      xp://peer.SSLSocket
   * @see      xp://peer.http.HttpConnection
   * @purpose  Transport
   */
  class SSLSocketHttpTransport extends SocketHttpTransport {

    /**
     * Creates a socket - overridden from parent class
     *
     * @param   peer.URL url
     * @param   string arg
     * @return  peer.Socket
     */
    protected function newSocket(URL $url, $arg) {
      sscanf($arg, 'v%d', $version);
      return new SSLSocket($url->getHost(), $url->getPort(443), NULL, $version);
    }
  }
?>
