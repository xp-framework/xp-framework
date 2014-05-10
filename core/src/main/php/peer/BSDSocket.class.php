<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.Socket');
  
  /**
   * BSDSocket implementation
   *
   * @test     xp://net.xp_framework.unittest.peer.sockets.BSDSocketTest
   * @see      php://sockets
   * @see      http://www.developerweb.net/sock-faq/ The UNIX Socket FAQ
   * @ext      sockets
   * @purpose  Provide an interface to the BSD sockets
   * @deprecated Use $impl parameter to peer.Socket instead!
   */
  class BSDSocket extends Socket {
    public
      $domain   = AF_INET,
      $type     = SOCK_STREAM,
      $protocol = SOL_TCP;
    
    static function __static() {
      defined('TCP_NODELAY') || define('TCP_NODELAY', 1);
    }

    /**
     * Set Domain
     *
     * @param   int domain one of AF_INET or AF_UNIX
     * @throws  lang.IllegalStateException if socket is already connected
     */
    public function setDomain($domain) {
      if ($this->isConnected()) {
        throw new IllegalStateException('Cannot set domain on connected socket');
      }
      $this->domain= $domain;
    }

    /**
     * Get Domain
     *
     * @return  int
     */
    public function getDomain() {
      return $this->domain;
    }

    /**
     * Set Type
     *
     * @param   int type one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM
     * @throws  lang.IllegalStateException if socket is already connected
     */
    public function setType($type) {
      if ($this->isConnected()) {
        throw new IllegalStateException('Cannot set type on connected socket');
      }
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @return  int
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Set Protocol
     *
     * @see     php://getprotobyname
     * @param   int protocol one of SOL_TCP or SOL_UDP
     * @throws  lang.IllegalStateException if socket is already connected
     */
    public function setProtocol($protocol) {
      if ($this->isConnected()) {
        throw new IllegalStateException('Cannot set protocol on connected socket');
      }
      $this->protocol= $protocol;
    }

    /**
     * Get Protocol
     *
     * @return  int
     */
    public function getProtocol() {
      return $this->protocol;
    }

    /**
     * Get last error
     *
     * @return  string error
     */  
    public function getLastError() {
      $e= socket_last_error($this->impl->handle());
      return sprintf('%d: %s', $e, socket_strerror($e));
    }
    
    /**
     * Set socket option
     *
     * @param   int level
     * @param   int name
     * @param   var value
     * @see     php://socket_set_option
     */
    public function setOption($level, $name, $value) {
      $this->impl->option($level, $name, $value);
    }
  }
?>
